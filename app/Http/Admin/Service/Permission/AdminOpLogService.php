<?php
declare(strict_types=1);

namespace App\Http\Admin\Service\Permission;

use App\Http\Admin\Model\AdminOpLog;
use App\Http\Admin\Repository\Permission\AdminOpLogRepository;
use App\Http\Common\Service\IService;

/**
 * 管理员操作日志
 *
 * @extends IService<AdminOpLog>
 */
final class AdminOpLogService extends IService
{
    public function __construct(
        protected readonly AdminOpLogRepository $repository,
    ) {}
}