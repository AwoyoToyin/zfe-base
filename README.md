# Doctrine ORM DB Abstraction

## Installation

```bash
$ composer require awoyotoyin/zfe-base
```

### Register the module

> ### Zend Expressive
>
> ```bash
> $aggregator = new ConfigAggregator([
>     ...
>     \Common\ConfigProvider::class,
>     ...
> ], $cacheConfig['config_cache_path']);
> ```

## Usage

### Entity Class

```bash
<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Common\Entity\AbstractEntity;

/**
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="blog_post")
 */
class Post extends AbstractEntity
{
    /**
     * @ORM\Column(name="title", type="string", length=32)
     * @var string
     */
    private $title;

    /**
     * Get the value of title
     *
     * @return  string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * Set the value of title
     *
     * @param  string  $title
     * @return self
     */
    public function setTitle(string $title): self
    {
        $this->title = $title;
        return $this;
    }
}
```

Your Entity class should extend `Common\Entity\AbstractEntity`.
The `Common\Entity\AbstractEntity` class defines the Id, CreatedAt and UpdatedAt attributes for your entity.
If you are extending from this class, your Entity class must define the lifecycle callbacks as it is required for both the CreatedAt and UpdatedAt attributes.

The `Common\Entity\AbstractEntity` class also exposes a `exchangeArray` method that takes an array as it's only argument and set your entity members from the array members.

```
Example:
$data = [
    'title' => 'Some Title'
];

$post = new Post();
$post->exchangeArray($data);
$post->getTitle(); // Some Title
```

### Provider Class

The Provider performs all database related operations hence, this class would mostly contain queries to your Entity.
All you have to do is define your Entity class and `Common\Provider\AbstractProvider` does all the heavy lifting

Available methods
```bash
public function fetchAll(): \Doctrine\ORM\ueryBuilder;

public function selectAll(array $filters = [], array $orderBy = [], array $groupBy = []): \Doctrine\ORM\QueryBuilder;

public function selectAllPaginate(
        $first = 0,
        $max = 20,
        array $filters = [],
        array $orderBy = [],
        array $groupBy = [],
        $fetchJoinCollection = true
    ): Doctrine\ORM\Tools\Pagination\Paginator;

public function selectJoin(
        $first = 0,
        $max = 20,
        array $filters = [],
        array $joins = [],
        array $orderBy = [],
        array $groupBy = []
    ): \Doctrine\ORM\QueryBuilder
```

```bash
<?php

/**
 * Description of PostProvider
 *
 * @author: Awoyo Oluwatoyin Stephen alias AwoyoToyin <awoyotoyin@gmail.com>
 */

namespace App\Provider;

use Common\Provider\AbstractProvider;

class PostProvider extends AbstractProvider
{
    protected $entityClass = 'App\Entity\Post';
}
```
### Service Class

```bash
<?php

/**
 * Description of PostService
 *
 * @author: Awoyo Oluwatoyin Stephen alias AwoyoToyin <awoyotoyin@gmail.com>
 */
namespace App\Service;

use Common\Service\AbstractService;

class PostService extends AbstractService
{

}
```
