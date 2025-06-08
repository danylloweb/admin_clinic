<?php

namespace App\Queues;

use Illuminate\Support\Facades\Log;
use RdKafka\Conf;
use RdKafka\KafkaConsumer;
use RdKafka\Producer;
use RdKafka\ProducerTopic;

class KafkaQueue
{
    private KafkaConsumer $consumer;
    private Producer $producer;
    private ProducerTopic $topic;

    private $brokers;

    /**
     * @param string $groupId
     * @param string $topicName
     */
    public function __construct(private readonly string $groupId, private readonly string $topicName) {
        $this->brokers = config('kafka.broker');

        $conf = new Conf();

        try {
            $conf->set('bootstrap.servers', $this->brokers);
            $conf->set('group.id', $this->groupId);
            $conf->set('auto.offset.reset', 'earliest');

            $this->consumer = new KafkaConsumer($conf);
            $this->consumer->subscribe([$this->topicName]);

            $producerConf = new Conf();
            $producerConf->set('bootstrap.servers', $this->brokers);
            $this->producer = new Producer($producerConf);
            $this->topic = $this->producer->newTopic($this->topicName);
        } catch (\Exception $exception) {
            Log::error('Failed to create instance queue', [
                'message' => $exception->getMessage()
            ]);
        }
    }

    /**
     * @return array|null
     */
    public function consume(): ?array
    {
        try {
            $event = $this->consumer->consume(-1);

            if ($event->err) {
                throw new \Exception($event->errstr());
            }

            if ($event->payload) {
                return[
                    'headers' => $event->headers,
                    'body' => json_decode($event->payload, true),
                ];
            }
        } catch (\Exception $exception) {
            Log::error('Failed to consumer message queue', [
                'message' => $exception->getMessage()
            ]);
        }

        return null;
    }

    /**
     * @param $publishMessageQueue
     * @return void
     */
    public function publish($publishMessageQueue): void
    {
        try {
            $this->topic->producev(
                RD_KAFKA_PARTITION_UA,
                0,
                json_encode($publishMessageQueue['body']),
                null,
                $publishMessageQueue['headers']
            );
            $this->producer->poll(0);
            Log::info('Kafka to send message queue', [
                'message' => "Message sent to Kafka",
                'data' => $publishMessageQueue['body']
            ]);
            for ($flushRetries = 0; $flushRetries < 10; ++$flushRetries) {
                $result = $this->producer->flush(1000);
                if (RD_KAFKA_RESP_ERR_NO_ERROR === $result) {
                    break;
                }
            }

            if (RD_KAFKA_RESP_ERR_NO_ERROR !== $result) {
                throw new \Exception('Unable to flush, messages might be lost!');
            }
        } catch (\Exception $exception) {
            Log::error('Failed to send message queue', [
                'message' => $exception->getMessage(),
                'data' => $publishMessageQueue['body']
            ]);
        }
    }
}
