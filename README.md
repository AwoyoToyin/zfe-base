# Doctrine ORM DB Abstraction

## Installation

```bash
$ composer require awoyotoyin/zfe-base "~1.0.0"
```

### Register the module

> ### Zend Expressive
>
> ```bash
> 
> use Zfe\Common\ConfigProvider as CommonConfigProvider;
> 
> $aggregator = new ConfigAggregator([
>     ...
>     CommonConfigProvider::class,
>     ...
> ], $cacheConfig['config_cache_path']);
> ```

## Usage

### Entity Class

```bash
<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Zfe\Common\Entity\AbstractEntity;

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

Your Entity class should extend `Zfe\Common\Entity\AbstractEntity`.
The `Zfe\Common\Entity\AbstractEntity` class defines the Id, CreatedAt and UpdatedAt attributes for your entity.
If you are extending from this class, your Entity class must define the lifecycle callbacks as it is required for both the CreatedAt and UpdatedAt attributes.

The `Zfe\Common\Entity\AbstractEntity` class also exposes a `exchangeArray` method that takes an array as it's only argument and set your entity members from the array members.

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
All you have to do is define your Entity class and `Zfe\Common\Provider\AbstractProvider` does all the heavy lifting

Available methods
```bash
public function fetchAll(): \Doctrine\ORM\QueryBuilder;

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
 * @author: Awoyo Oluwatoyin Stephen alias awoyotoyin <awoyotoyin@gmail.com>
 */

namespace App\Provider;

use Zfe\Common\Provider\AbstractProvider;

class PostProvider extends AbstractProvider
{
    protected $entityClass = 'App\Entity\Post';

    protected $entity_event_prefix = 'blog_post';
}
```
### Service Class

```bash
<?php

/**
 * Description of PostService
 *
 * @author: Awoyo Oluwatoyin Stephen alias awoyotoyin <awoyotoyin@gmail.com>
 */
namespace App\Service;

use Zfe\Common\Service\AbstractService;

class PostService extends AbstractService
{

}
```

### System Events

This library ships with the `Zend\EventManager\EventManager`.
By default, there are 4 built in events that are triggered before and after the following operations:

```bash
> Saving an Entity
> entity_save_before event fired before an entity is saved
> entity_save_after event fired after an entity is saved
>
> Deleting an Entity
> entity_delete_before event fired before an entity is deleted
> entity_delete_after event fired after an entity is deleted
```

To fire off entity specific event, your provider class must set the value of the `$entity_event_prefix` property. See `PostProvider` definition above.
In which case, the following events are now available to us as well:

```bash
> Saving an Entity
> blog_post_save_before event fired before an entity is saved
> blog_post_save_after event fired after an entity is saved
>
> Deleting an Entity
> blog_post_delete_before event fired before an entity is deleted
> blog_post_delete_after event fired after an entity is deleted
```

To register your own custom event listener, create a `.config.php` file with contents similar to own below:

```bash
<?php

return [
    'listeners' => [
        'events' => [
            // the event we are listening to
            'blog_post_save_before' => [
                'class' => \App\Observer\PostObserver::class, // points to the observer class
                'method' => 'onPostBeforeSaveHandled' // points to the method handling the event
            ]
        ],
    ]
];

```
There is a sample `events.config.php.dist` file included in the `config` folder.

The `App\Observer\PostObserver` defined above could contain the below code. Replace with your logic

```bash
<?php
/**
 * Description of PostObserver
 *
 * @author: Awoyo Oluwatoyin Stephen alias awoyotoyin <awoyotoyin@gmail.com>
 */
namespace App\Observer;

use Interop\Container\ContainerInterface;
use Zend\EventManager\Event;
use Zend\Log\Logger;
use Zend\Log\Processor\PsrPlaceholder;
use Zend\Log\Writer;

class PostObserver
{
    public function __invoke(ContainerInterface $container)
    {
        // Grab some dependencies from the $container
        // And return self
        return new self();
    }

    public function onPostBeforeSaveHandled(Event $event)
    {
        // Do something with the $event here
        $name = $event->getName();
        $target = get_class($event->getTarget());
        $entity = $event->getParam('entity');

        /** Modify the Entity */
        if ($entity instanceof \Zfe\Common\Entity\EntityInterface) {
            $entity->setTitle('Title Changed');
        } elseif (is_array($entity)) {
            $entity['title'] = 'Title Changed';
        }

        /** Push changes back to the trigger */
        $event->setParam('entity', $entity);

        $logger = new Logger;
        $logger->addProcessor(new PsrPlaceholder);

        $writer = new Writer\Stream('data/log/events.log');
        $logger->addWriter($writer);

        $logger->notice('{event} was called on {target} with entity {entity}', [
            'event' => $event,
            'target' => $target,
            'entity' => json_encode($entity)
        ]);
    }
}

```
