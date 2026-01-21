<?php
declare(strict_types=1);

namespace App\Http\Common\Repository\Traits;

use Hyperf\Context\ApplicationContext;
use Hyperf\Snowflake\IdGeneratorInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * 雪花ID Trait
 */
trait HasSnowflakeTrait
{
    /**
     * 雪花ID字段名
     *
     * @var string
     */
    protected string $snowflakeKeyName = 'snowflake_id';

    /**
     * 是否启用雪花ID
     *
     * @var bool
     */
    protected bool $enableSnowflake = true;

    /**
     * 创建时生成雪花ID
     *
     * 如果模型需要额外处理 creating 事件，请在模型处额外处理，如下：
     * class User extends Model {
     *      use Snowflake {
     *          creating as create;
     *      }
     *
     *      public function creating()
     *      {
     *          $this->create();
     *      }
     * }
     *
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function creating(): void
    {
        // 不启用雪花ID
        if (!$this->enableSnowflake) {
            return;
        }

        // 字段不在模型中
        if (!in_array($this->snowflakeKeyName, $this->getFillable())) {
            return;
        }

        // 字段已存在值
        if (!empty($this->{$this->snowflakeKeyName})) {
            return;
        }

        $container = ApplicationContext::getContainer();
        $generator = $container->get(IdGeneratorInterface::class);
        $this->{$this->snowflakeKeyName} = $generator->generate();
    }
}