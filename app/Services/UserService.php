<?php

namespace App\Services;

use App\Criterias\AppRequestCriteria;
use App\Criterias\FilterByHasMedicalCriteria;
use App\Entities\UserType;
use App\Models\UserGateway;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Log;
use Prettus\Repository\Exceptions\RepositoryException;

/**
 * UserService
 */
class UserService extends AppService
{
    /**
     * @var UserRepository
     */
    protected $repository;
    protected UserGateway $userGateway;

    /**
     * @param UserRepository $repository
     * @param UserGateway $userGateway
     */
    public function __construct(UserRepository $repository, UserGateway $userGateway)
    {
        $this->repository  = $repository;
        $this->userGateway = $userGateway;
    }

    /**
     * @param int $limit
     * @return mixed
     * @throws RepositoryException
     */
    public function all(int $limit = 20)
    {
        return $this->repository
            ->resetCriteria()
            ->pushCriteria(app(FilterByHasMedicalCriteria::class))
            ->pushCriteria(app(AppRequestCriteria::class))
            ->paginate($limit);
    }


    /**
     * @param int $phone
     * @return bool[]
     */
    public function checkPhone(int $phone): array
    {
        $exist = $this->repository->skipPresenter()->findWhere(['phone' => $phone])->first();

        return [
            'existe' => !empty($exist)
        ];
    }

    public function configUserGateway()
    {
        $users = $this->repository->skipPresenter()->all();

        foreach ($users as $user) {
            $userGate = $this->userGateway->where('email',$user->email)->first();
            if ($user->user_type_id == 1){
                $userGate->user_type_id = 1;
            }else{
                $userGate->user_type_id = 3;
            }
            $userGate->save();
        }
    }

    /**
     * @param array $data
     * @param bool $skipPresenter
     * @return mixed
     */
    public function create(array $data, bool $skipPresenter = false): mixed
    {
        if (isset($data['password']) && !empty($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        }
        if (!$this->isEmpty($data['img'])){
            $data['img'] = $this->putPhotoProfileUser($data['img']);
        }
        $replace = ['.','-'];
        $data['cpf'] = str_replace($replace,'',$data['cpf']);
        $data['user_type_id'] = UserType::MEDICAL;
        return $this->repository->create($data);
    }

    /**
     * @param array $data
     * @param $id
     * @param bool $skipPresenter
     * @return array|mixed
     */
    public function update(array $data, $id, bool $skipPresenter = false): mixed
    {
        try {
            if (!$this->isEmpty($data['img'])){
                $user = $this->repository->skipPresenter()->find($id);
                $this->deleteFileS3($user->img);
                $data['img'] = $this->putPhotoProfileUser($data['img']);
            }
            return parent::update($data, $id, $skipPresenter);
        }catch (\Exception $exception) {
            Log::info($exception->getMessage());
            return $data;
        }

    }

    /**
     * @param string $file
     * @return string
     */
    public function putPhotoProfileUser(string $file):string
    {
        $content              = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $file));;
        $file_name_content    = 'photo_profile/' .date('dmYHis').uniqid(10).".jpeg";
        return $this->putFileS3($file_name_content, $content);
    }


}
