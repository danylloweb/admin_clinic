# Application Service Omni Hub Api


## Setup

Use the make command to start

```bash
make up
```

## Introdução

## Tabela de conteudo

- <a href="#installation">Instalação</a>
    - <a href="#composer">Composer</a>
    - <a href="#laravel">Laravel</a>
- <a href="#methods">Methods</a>
    - <a href="#prettusrepositorycontractsrepositoryinterface">RepositoryInterface</a>
    - <a href="#prettusrepositorycontractsrepositorycriteriainterface">RepositoryCriteriaInterface</a>
    - <a href="#prettusrepositorycontractscacheableinterface">CacheableInterface</a>
    - <a href="#prettusrepositorycontractspresenterinterface">PresenterInterface</a>
    - <a href="#prettusrepositorycontractscriteriainterface">CriteriaInterface</a>
- <a href="#usage">Usage</a>
    - <a href="#create-a-model">Create a Model</a>
    - <a href="#create-a-repository">Create a Repository</a>
    - <a href="#generators">Generators</a>
    - <a href="#use-methods">Use methods</a>
    - <a href="#create-a-criteria">Create a Criteria</a>
    - <a href="#using-the-criteria-in-a-controller">Using the Criteria in a Controller</a>
    - <a href="#using-the-requestcriteria">Using the RequestCriteria</a>
- <a href="#cache">Cache</a>
    - <a href="#cache-usage">Usage</a>
    - <a href="#cache-config">Config</a>
- <a href="#validators">Validators</a>
    - <a href="#using-a-validator-class">Using a Validator Class</a>
        - <a href="#create-a-validator">Create a Validator</a>
        - <a href="#enabling-validator-in-your-repository-1">Enabling Validator in your Repository</a>
    - <a href="#defining-rules-in-the-repository">Defining rules in the repository</a>
- <a href="#presenters">Presenters</a>
    - <a href="#fractal-presenter">Fractal Presenter</a>
        - <a href="#create-a-presenter">Create a Fractal Presenter</a>
        - <a href="#implement-interface">Model Transformable</a>
    - <a href="#enabling-in-your-repository-1">Enabling in your Repository</a>



## Methods ultilizados na aplicação

### Prettus\Repository\Contracts\RepositoryInterface

- all($columns = array('*'))
- first($columns = array('*'))
- paginate($limit = null, $columns = ['*'])
- find($id, $columns = ['*'])
- findByField($field, $value, $columns = ['*'])
- findWhere(array $where, $columns = ['*'])
- findWhereIn($field, array $where, $columns = [*])
- findWhereNotIn($field, array $where, $columns = [*])
- findWhereBetween($field, array $where, $columns = [*])
- create(array $attributes)
- update(array $attributes, $id)
- updateOrCreate(array $attributes, array $values = [])
- delete($id)
- deleteWhere(array $where)
- orderBy($column, $direction = 'asc');
- with(array $relations);
- has(string $relation);
- whereHas(string $relation, closure $closure);
- hidden(array $fields);
- visible(array $fields);
- scopeQuery(Closure $scope);
- getFieldsSearchable();
- setPresenter($presenter);
- skipPresenter($status = true);


### Prettus\Repository\Contracts\RepositoryCriteriaInterface

- pushCriteria($criteria)
- popCriteria($criteria)
- getCriteria()
- getByCriteria(CriteriaInterface $criteria)
- skipCriteria($status = true)
- getFieldsSearchable()

### Prettus\Repository\Contracts\CacheableInterface

- setCacheRepository(CacheRepository $repository)
- getCacheRepository()
- getCacheKey($method, $args = null)
- getCacheTime()
- skipCache($status = true)

### Prettus\Repository\Contracts\PresenterInterface

- present($data);

### Prettus\Repository\Contracts\Presentable

- setPresenter(PresenterInterface $presenter);
- presenter();

### Prettus\Repository\Contracts\CriteriaInterface

- apply($model, RepositoryInterface $repository);

### Prettus\Repository\Contracts\Transformable

- transform();


## Usage

### Criação de Modelo/Entidade

Create your model normally, but it is important to define the attributes that can be filled from the input form data.

```php
namespace App;

class Post extends Eloquent { // or Ardent, Or any other Model Class

    protected $fillable = [
        'title',
        'author',
        ...
     ];

     ...
}
```

### Criação de repositório

```php
namespace App;

use Prettus\Repository\Eloquent\BaseRepository;

class PostRepository extends BaseRepository {

    /**
     * Specify Model class name
     *
     * @return string
     */
    function model()
    {
        return "App\\Post";
    }
}
```

### Gerador

Create your repositories easily through the generator.

#### Configuração

Você deve primeiro configurar o local de armazenamento dos arquivos do repositório. Por padrão é a pasta "app" e o namespace "App". Por favor, note que os valores no array `paths` são usados ​​tanto como *namespace* quanto como caminhos de arquivo. Relaxe, porém, as barras invertidas e invertidas são tratadas durante a geração.

```php
    ...
    'generator'=>[
        'basePath'=>app()->path(),
        'rootNamespace'=>'App\\',
        'paths'=>[
            'models'       => 'Entities',
            'repositories' => 'Repositories',
            'interfaces'   => 'Repositories',
            'transformers' => 'Transformers',
            'presenters'   => 'Presenters',
            'validators'   => 'Validators',
            'controllers'  => 'Http/Controllers',
            'provider'     => 'RepositoryServiceProvider',
            'criteria'     => 'Criteria',
        ]
    ]
```

Você pode querer salvar a raiz da pasta do seu projeto fora do aplicativo e adicionar outro namespace, por exemplo

```php
    ...
     'generator'=>[
        'basePath'      => base_path('src/Lorem'),
        'rootNamespace' => 'Lorem\\'
    ]
```

Além disso, você pode personalizar onde suas classes geradas serão salvas. Isso pode ser feito editando o nó `paths` ao seu gosto. Por exemplo:

```php
    'generator'=>[
        'basePath'=>app()->path(),
        'rootNamespace'=>'App\\',
        'paths'=>[
            'models'=>'Models',
            'repositories'=>'Repositories\\Eloquent',
            'interfaces'=>'Contracts\\Repositories',
            'transformers'=>'Transformers',
            'presenters'=>'Presenters'
            'validators'   => 'Validators',
            'controllers'  => 'Http/Controllers',
            'provider'     => 'RepositoryServiceProvider',
            'criteria'     => 'Criteria',
        ]
    ]
```

#### Commands

Para gerar tudo o que você precisa para o seu modelo, execute este comando:

```terminal
php artisan make:entity Post
```

Isso criará as classes Controller, Validator, Model, Repository, Presenter e Transformer.
Ele também criará um novo provedor de serviços que será usado para vincular o Repositório Eloquent com sua Interface de Repositório correspondente.
Para carregá-lo, basta adicionar isso ao seu método AppServiceProvider@register

```php
    $this->app->register(RepositoryServiceProvider::class);
```

Você também pode passar as opções do ```repository``` comando, já que este comando é apenas um wrapper.

Para gerar um repositório para seu modelo Post, use o seguinte comando

```terminal
php artisan make:repository Post
```

Para gerar um repositório para seu modelo Post com namespace Blog, use o seguinte comando

```terminal
php artisan make:repository "Blog\Post"
```

Campos adicionados que são preenchíveis

```terminal
php artisan make:repository "Blog\Post" --fillable="title,content"
```

Para adicionar regras de validação diretamente com seu comando, você precisa passar o `--rules` opção e criar migrações também:


```terminal
php artisan make:entity Cat --fillable="title:string,content:text" --rules="title=>required|min:2, content=>sometimes|min:10"
```

O comando também criará seu controlador RESTfull básico, então apenas adicione esta linha ao seu `routes.php` arquivo e você terá um CRUD básico:

 ```php
 Route::resource('cats', CatsController::class);
 ```


Ao executar o comando, você estará criando a pasta "Entidades" e "Repositórios" dentro da pasta que você definiu como padrão.

Feito isso, você ainda precisa vincular sua interface ao seu repositório real, por exemplo, em seu próprio Provedor de Serviços de Repositórios.
```php
App::bind('{YOUR_NAMESPACE}Repositories\PostRepository', '{YOUR_NAMESPACE}Repositories\PostRepositoryEloquent');
```

E depois usa

```php
public function __construct({YOUR_NAMESPACE}Repositories\PostRepository $repository){
    $this->repository = $repository;
}
```

Como alternativa, você pode usar o comando craft para fazer a ligação para você.

```php
php artisan make:bindings Cats
```

### Use methods

```php
namespace App\Http\Controllers;

use App\PostRepository;

class PostsController extends BaseController {

    /**
     * @var PostRepository
     */
    protected $repository;

    public function __construct(PostRepository $repository){
        $this->repository = $repository;
    }

    ....
}
```

Encontrar todos os resultados no Repositório
```php
$posts = $this->repository->all();
```

Encontre todos os resultados no Repositório com paginação

```php
$posts = $this->repository->paginate($limit = null, $columns = ['*']);
```

Localizar por resultado por id

```php
$post = $this->repository->find($id);
```

Ocultando atributos do modelo

```php
$post = $this->repository->hidden(['country_id'])->find($id);
```

Mostrando apenas atributos específicos do modelo

```php
$post = $this->repository->visible(['id', 'state_id'])->find($id);
```

Carregando os relacionamentos do modelo

```php
$post = $this->repository->with(['state'])->find($id);
```

Localizar por resultado por nome de campo

```php
$posts = $this->repository->findByField('country_id','15');
```

Localizar por resultado por vários campos

```php
$posts = $this->repository->findWhere([
    //Default Condition =
    'state_id'=>'10',
    'country_id'=>'15',

    //Custom Condition
    ['columnName1','>','10'],

    //DATE, DAY, MONTH, YEAR
    ['columnName2','DATE','2021-07-02'], //whereDate
    ['columnName3','DATE >=','2021-07-02'], //whereDate with operator

    ['columnName4','IN',['value1','value2']], //whereIn
    ['columnName5','NOTIN',['value1','value2']], //whereNotIn
    ['columnName6','EXIST',''], //whereExists
    
    //HAS, HASMORPH, DOESNTHAVE, DOESNTHAVEMORPH
    ['columnName7','HAS',function($query){}], //whereHas

    //BETWEEN, BETWEENCOLUMNS, NOTBETWEEN, NOTBETWEENCOLUMNS
    ['columnName8','BETWEEN',[10, 100]], //whereBetween
]);
```
Localizar por resultado por vários valores em um campo

```php
$posts = $this->repository->findWhereIn('id', [1,2,3,4,5]);
```

Encontre por resultado excluindo vários valores em um campo

```php
$posts = $this->repository->findWhereNotIn('id', [6,7,8,9,10]);
```

Encontre tudo usando o escopo personalizado

```php
$posts = $this->repository->scopeQuery(function($query){
    return $query->orderBy('sort_order','asc');
})->all();
```

Criar nova entrada no repositório

```php
$post = $this->repository->create( Input::all() );
```

Atualizar entrada no repositório

```php
$post = $this->repository->update( Input::all(), $id );
```

Excluir entrada no repositório

```php
$this->repository->delete($id)
```

Excluir entrada no repositório por vários campos

```php
$this->repository->deleteWhere([
    //Default Condition =
    'state_id'=>'10',
    'country_id'=>'15',
])
```

### Criar um Critério

#### Usando o comando

```terminal
php artisan make:criteria MyCriteria
```

Os critérios são uma forma de alterar o repositório da consulta aplicando condições específicas de acordo com as suas necessidades. Você pode adicionar vários critérios em seu repositório.

```php

use Prettus\Repository\Contracts\RepositoryInterface;
use Prettus\Repository\Contracts\CriteriaInterface;

class MyCriteria implements CriteriaInterface {

    public function apply($model, RepositoryInterface $repository)
    {
        $model = $model->where('user_id','=', Auth::user()->id );
        return $model;
    }
}
```

### Usando os critérios em um controlador

```php

namespace App\Http\Controllers;

use App\PostRepository;

class PostsController extends BaseController {

    /**
     * @var PostRepository
     */
    protected $repository;

    public function __construct(PostRepository $repository){
        $this->repository = $repository;
    }


    public function index()
    {
        $this->repository->pushCriteria(new MyCriteria1());
        $this->repository->pushCriteria(MyCriteria2::class);
        $posts = $this->repository->all();
		...
    }

}
```

Obtendo resultados de Critérios

```php
$posts = $this->repository->getByCriteria(new MyCriteria());
```

Definindo os critérios padrão no repositório

```php
use Prettus\Repository\Eloquent\BaseRepository;

class PostRepository extends BaseRepository {

    public function boot(){
        $this->pushCriteria(new MyCriteria());
        // or
        $this->pushCriteria(AnotherCriteria::class);
        ...
    }

    function model(){
       return "App\\Post";
    }
}
```

### Ignorar critérios definidos no repositório

Use `skipCriteria` antes de qualquer outro método de encadeamento

```php
$posts = $this->repository->skipCriteria()->all();
```

### Popping criteria

Use `popCriteria` remover um critério

```php
$this->repository->popCriteria(new Criteria1());
// or
$this->repository->popCriteria(Criteria1::class);
```


### Usando o RequestCriteria

RequestCriteria é uma implementação Criteria padrão. Permite a atuação de filtros no repositório a partir de parâmetros enviados na requisição.

Você pode realizar uma pesquisa dinâmica, filtrar os dados e personalizar as consultas.

Para usar o Criteria em seu repositório, você pode adicionar um novo critério no método de boot do seu repositório, ou usar diretamente no seu controller, para filtrar apenas algumas requisições.

#### Ativando em seu repositório

```php
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;


class PostRepository extends BaseRepository {

	/**
     * @var array
     */
    protected $fieldSearchable = [
        'name',
        'email'
    ];

    public function boot(){
        $this->pushCriteria(app('Prettus\Repository\Criteria\RequestCriteria'));
        ...
    }

    function model(){
       return "App\\Post";
    }
}
```

Lembre-se, você precisa definir quais campos do modelo podem ser pesquisáveis.

Em seu repositório, defina **$fieldSearchable** com o nome dos campos a serem pesquisáveis ​​ou uma relação com os campos.

```php
protected $fieldSearchable = [
	'name',
	'email',
	'product.name'
];
```

Você pode definir o tipo de condição que será usada para realizar a consulta, a condição padrão é "**=**"

```php
protected $fieldSearchable = [
	'name'=>'like',
	'email', // Default Condition "="
	'your_field'=>'condition'
];
```


#### Ativando no seu controlador

```php
	public function index()
    {
        $this->repository->pushCriteria(app('Prettus\Repository\Criteria\RequestCriteria'));
        $posts = $this->repository->all();
		...
    }
```

#### Exemplo dos Critérios

Solicitar todos os dados sem filtro por solicitação

`http://perfectpaytest.local/users`

```json
[
    {
        "id": 1,
        "name": "John Doe",
        "email": "john@gmail.com",
        "created_at": "-0001-11-30 00:00:00",
        "updated_at": "-0001-11-30 00:00:00"
    },
    {
        "id": 2,
        "name": "Lorem Ipsum",
        "email": "lorem@ipsum.com",
        "created_at": "-0001-11-30 00:00:00",
        "updated_at": "-0001-11-30 00:00:00"
    },
    {
        "id": 3,
        "name": "Laravel",
        "email": "laravel@gmail.com",
        "created_at": "-0001-11-30 00:00:00",
        "updated_at": "-0001-11-30 00:00:00"
    }
]
```

Pesquisando no repositório

`http://perfectpaytest.local/users?search=John%20Doe`

or

`http://perfectpaytest.local/users?search=John&searchFields=name:like`

or

`http://perfectpaytest.local/users?search=john@gmail.com&searchFields=email:=`

or

`http://perfectpaytest.local/users?search=name:John Doe;email:john@gmail.com`

or

`http://perfectpaytest.local/users?search=name:John;email:john@gmail.com&searchFields=name:like;email:=`

```json
[
    {
        "id": 1,
        "name": "John Doe",
        "email": "john@gmail.com",
        "created_at": "-0001-11-30 00:00:00",
        "updated_at": "-0001-11-30 00:00:00"
    }
]
```


Você pode usar parâmetros "search" sem parâmetros completos "searchFields".

`http://perfectpaytest.local/users?search=id:2;age:17;email:john@gmail.com&searchFields='id':=`

Por padrão, RequestCriteria faz suas consultas usando o operador de comparação **OR** para cada parâmetro de consulta.

`http://perfectpaytest.local/users?search=age:17;email:john@gmail.com`

O exemplo acima executará a seguinte consulta:

``` sql
SELECT * FROM users WHERE age = 17 OR email = 'john@gmail.com';
```

Para que ele faça a consulta usando o **AND**, passe o parâmetro *searchJoin* conforme abaixo:

`http://perfectpaytest.local/users?search=age:17;email:john@gmail.com&searchJoin=and`


Campos de filtragem

`http://perfectpaytest.local/users?filter=id;name`

```json
[
    {
        "id": 1,
        "name": "John Doe"
    },
    {
        "id": 2,
        "name": "Lorem Ipsum"
    },
    {
        "id": 3,
        "name": "Laravel"
    }
]
```

Classificando os resultados

`http://perfectpaytest.local/users?filter=id;name&orderBy=id&sortedBy=desc`

```json
[
    {
        "id": 3,
        "name": "Laravel"
    },
    {
        "id": 2,
        "name": "Lorem Ipsum"
    },
    {
        "id": 1,
        "name": "John Doe"
    }
]
```

Classificando através de tabelas relacionadas

`http://perfectpaytest.local/users?orderBy=posts|title&sortedBy=desc`

A consulta terá algo assim

```sql
...
INNER JOIN posts ON users.post_id = posts.id
...
ORDER BY title
...
```

`http://perfectpaytest.local/users?orderBy=posts:custom_id|posts.title&sortedBy=desc`

A consulta terá algo assim

```sql
...
INNER JOIN posts ON users.custom_id = posts.id
...
ORDER BY posts.title
...
```

`http://perfectpaytest.local/users?orderBy=posts:custom_id,other_id|posts.title&sortedBy=desc`

A consulta terá algo assim

```sql
...
INNER JOIN posts ON users.custom_id = posts.other_id
...
ORDER BY posts.title
...
```

Classificando várias colunas da mesma forma sortedBy

`http://perfectpaytest.local/users?orderBy=name;created_at&sortedBy=desc`

Resultado terá algo assim

```json
   [
       {
           "id": 1,
           "name": "Laravel",
           "created_at": "-0001-11-29 00:00:00"
       },
       {
           "id": 3,
           "name": "Laravel",
           "created_at": "-0001-11-28 00:00:00"
       },
       {
           "id": 2,
           "name": "John Doe",
           "created_at": "-0001-11-30 00:00:00"
       }
   ]
```


Classificando a diferença de várias colunas sortedBy

`http://perfectpaytest.local/users?orderBy=name;created_at&sortedBy=desc;asc`

Resultado terá algo assim

```json
   [
       {
           "id": 3,
           "name": "Laravel",
           "created_at": "-0001-11-28 00:00:00"
       },
       {
           "id": 1,
           "name": "Laravel",
           "created_at": "-0001-11-29 00:00:00"
       },
       {
           "id": 2,
           "name": "John Doe",
           "created_at": "-0001-11-30 00:00:00"
       }
   ]
```

Adicionar relacionamento

`http://perfectpaytest.local/users?with=groups`

Entre filtro

`http://perfectpaytest.local/product?search=price:100,500&searchFields=price:between`

Resultado terá algo assim

```json
   [
       {
           "id": 3,
           "price": "150",
           "created_at": "-0001-11-28 00:00:00"
       },
       {
           "id": 1,
           "price": "300",
           "created_at": "-0001-11-29 00:00:00"
       },
       {
           "id": 2,
           "price": "450",
           "created_at": "-0001-11-30 00:00:00"
       }
   ]
```

Filtro WhereIn

`http://perfectpaytest.local/product?search=price:300,500&searchFields=price:in`

Resultado terá algo assim

```json
   [
       {
           "id": 1,
           "price": "300",
           "created_at": "-0001-11-29 00:00:00"
       }
   ]
```

#### Overwrite params name

Você pode alterar o nome dos parâmetros no arquivo de configuração **config/repository.php**

### Cache

Adicione uma camada de cache facilmente ao seu repositório

#### Cache Usage

Implementa a interface CacheableInterface e usa CacheableRepository Trait.

```php
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Contracts\CacheableInterface;
use Prettus\Repository\Traits\CacheableRepository;

class PostRepository extends BaseRepository implements CacheableInterface {

    use CacheableRepository;

    ...
}
```


Feito , feito que seu repositório será armazenado em cache , e o cache do repositório será limpo sempre que um item for criado, modificado ou excluído

#### Cache Config

Você pode alterar as configurações de cache no arquivo *config/repository.php* e também diretamente no seu repositório.

*config/repository.php*

```php
'cache'=>[
    //Enable or disable cache repositories
    'enabled'   => true,

    //Lifetime of cache
    'minutes'   => 30,

    //Repository Cache, implementation Illuminate\Contracts\Cache\Repository
    'repository'=> 'cache',

    //Sets clearing the cache
    'clean'     => [
        //Enable, disable clearing the cache on changes
        'enabled' => true,

        'on' => [
            //Enable, disable clearing the cache when you create an item
            'create'=>true,

            //Enable, disable clearing the cache when upgrading an item
            'update'=>true,

            //Enable, disable clearing the cache when you delete an item
            'delete'=>true,
        ]
    ],
    'params' => [
        //Request parameter that will be used to bypass the cache repository
        'skipCache'=>'skipCache'
    ],
    'allowed'=>[
        //Allow caching only for some methods
        'only'  =>null,

        //Allow caching for all available methods, except
        'except'=>null
    ],
],
```

É possível substituir essas configurações diretamente no repositório.

```php
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Contracts\CacheableInterface;
use Prettus\Repository\Traits\CacheableRepository;

class PostRepository extends BaseRepository implements CacheableInterface {

    // Setting the lifetime of the cache to a repository specifically
    protected $cacheMinutes = 90;

    protected $cacheOnly = ['all', ...];
    //or
    protected $cacheExcept = ['find', ...];

    use CacheableRepository;

    ...
}
```

Os métodos que podem ser armazenados em cache são : all, paginate, find, findByField, findWhere, getByCriteria

### Validators

Requer [prettus/laravel-validator](https://github.com/prettus/laravel-validator). `composer require prettus/laravel-validator`

Fácil validação com `prettus/laravel-validator`

[For more details click here](https://github.com/prettus/laravel-validator)

#### Usando uma classe validadora

##### Criar um validador

No exemplo abaixo, definimos algumas regras tanto para criação quanto para edição

```php
use \Prettus\Validator\LaravelValidator;

class PostValidator extends LaravelValidator {

    protected $rules = [
        'title' => 'required',
        'text'  => 'min:3',
        'author'=> 'required'
    ];

}
```

Para definir regras específicas, proceda conforme abaixo:

```php
use \Prettus\Validator\Contracts\ValidatorInterface;
use \Prettus\Validator\LaravelValidator;

class PostValidator extends LaravelValidator {

    protected $rules = [
        ValidatorInterface::RULE_CREATE => [
            'title' => 'required',
            'text'  => 'min:3',
            'author'=> 'required'
        ],
        ValidatorInterface::RULE_UPDATE => [
            'title' => 'required'
        ]
   ];

}
```

##### 

Ativando o Validator em seu repositório

```php
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;

class PostRepository extends BaseRepository {

    /**
     * Specify Model class name
     *
     * @return mixed
     */
    function model(){
       return "App\\Post";
    }

    /**
     * Specify Validator class name
     *
     * @return mixed
     */
    public function validator()
    {
        return "App\\PostValidator";
    }
}
```

#### Definindo regras no repositório

Como alternativa, em vez de usar uma classe para definir suas regras de validação, você pode definir suas regras diretamente na propriedade do repositório de regras, terá o mesmo efeito que uma classe de validação.

```php
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Validator\Contracts\ValidatorInterface;

class PostRepository extends BaseRepository {

    /**
     * Specify Validator Rules
     * @var array
     */
     protected $rules = [
        ValidatorInterface::RULE_CREATE => [
            'title' => 'required',
            'text'  => 'min:3',
            'author'=> 'required'
        ],
        ValidatorInterface::RULE_UPDATE => [
            'title' => 'required'
        ]
   ];

    /**
     * Specify Model class name
     *
     * @return mixed
     */
    function model(){
       return "App\\Post";
    }

}
```

A validação está pronta. Em caso de falha será dada uma exceção do tipo: *Prettus\Validator\Exceptions\ValidatorException*

### Presenters

Os apresentadores funcionam como um wrapper e renderizador para objetos.

#### Fractal Presenter

Requer [Fractal](http://fractal.thephpleague.com/). `composer require league/fractal`

Há duas maneiras de implementar o Presenter, a primeira é criar um TransformerAbstract e defini-lo usando sua classe Presenter, conforme descrito em Criar uma classe Transformer.

A segunda maneira é fazer com que seu modelo implemente a interface Transformable e use o Presenter ModelFractarPresenter padrão, isso terá o mesmo efeito.

##### classe do transformador

###### Crie um Transformer usando o comando

```terminal
php artisan make:transformer Post
```


Isso irá gerar a classe abaixo.

###### Criar uma Classe Transformer

```php
use League\Fractal\TransformerAbstract;

class PostTransformer extends TransformerAbstract
{
    public function transform(\Post $post)
    {
        return [
            'id'      => (int) $post->id,
            'title'   => $post->title,
            'content' => $post->content
        ];
    }
}
```

###### Crie um apresentador usando o comando

```terminal
php artisan make:presenter Post
```

O comando solicitará que você crie um Transformer também, caso ainda não o tenha feito.
###### Create a Presenter

```php
use Prettus\Repository\Presenter\FractalPresenter;

class PostPresenter extends FractalPresenter {

    /**
     * Prepare data to present
     *
     * @return \League\Fractal\TransformerAbstract
     */
    public function getTransformer()
    {
        return new PostTransformer();
    }
}
```

###### Ativando em seu repositório

```php
use Prettus\Repository\Eloquent\BaseRepository;

class PostRepository extends BaseRepository {

    ...

    public function presenter()
    {
        return "App\\Presenter\\PostPresenter";
    }
}
```

Ou ative-o em seu controlador com

```php
$this->repository->setPresenter("App\\Presenter\\PostPresenter");
```

###### Usando o apresentador depois do modelo

Se você gravou um apresentador e em algum momento usou o método `skipPresenter()` ou simplesmente não quer que seu resultado seja alterado automaticamente pelo apresentador.
Você pode implementar a interface Presentable em seu modelo para poder apresentá-lo a qualquer momento. Veja abaixo:

Em seu modelo, implemente a interface `Prettus\Repository\Contracts\Presentable` e `Prettus\Repository\Traits\PresentableTrait`
```php
namespace App;

use Prettus\Repository\Contracts\Presentable;
use Prettus\Repository\Traits\PresentableTrait;

class Post extends Eloquent implements Presentable {

    use PresentableTrait;

    protected $fillable = [
        'title',
        'author',
        ...
     ];

     ...
}
```

Pronto, agora você pode enviar seu Modelo individualmente, Veja um exemplo:

```php
$repository = app('App\PostRepository');
$repository->setPresenter("Prettus\\Repository\\Presenter\\ModelFractalPresenter");

//Getting the result transformed by the presenter directly in the search
$post = $repository->find(1);

print_r( $post ); //It produces an output as array

...

//Skip presenter and bringing the original result of the Model
$post = $repository->skipPresenter()->find(1);

print_r( $post ); //It produces an output as a Model object
print_r( $post->presenter() ); //It produces an output as array

```

Você pode pular o apresentador a cada visita e usá-lo sob demanda diretamente no modelo, para isso defina o atributo `$skipPresenter` como verdadeiro em seu repositório:


```php
use Prettus\Repository\Eloquent\BaseRepository;

class PostRepository extends BaseRepository {

    /**
    * @var bool
    */
    protected $skipPresenter = true;

    public function presenter()
    {
        return "App\\Presenter\\PostPresenter";
    }
}
```

##### Model Class

###### Implement Interface

```php
namespace App;

use Prettus\Repository\Contracts\Transformable;

class Post extends Eloquent implements Transformable {
     ...
     /**
      * @return array
      */
     public function transform()
     {
         return [
             'id'      => (int) $this->id,
             'title'   => $this->title,
             'content' => $this->content
         ];
     }
}
```

###### Ativando em seu repositório

`Prettus\Repository\Presenter\ModelFractalPresenter` é um padrão do Presenter para modelos implementando Transformable

```php
use Prettus\Repository\Eloquent\BaseRepository;

class PostRepository extends BaseRepository {

    ...

    public function presenter()
    {
        return "Prettus\\Repository\\Presenter\\ModelFractalPresenter";
    }
}
```

Ou ative-o em seu controlador com

```php
$this->repository->setPresenter("Prettus\\Repository\\Presenter\\ModelFractalPresenter");
```

### Ignorar apresentador definido no repositório

Use *skipPresenter* antes de qualquer outro método de encadeamento

```php
$posts = $this->repository->skipPresenter()->all();
```

ou

```php
$this->repository->skipPresenter();

$posts = $this->repository->all();
```
