<?php

namespace App\Aliyun;

use AlibabaCloud\Client\AlibabaCloud;
use Illuminate\Support\ServiceProvider;

class AliyunServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        AlibabaCloud::accessKeyClient(config('filesystems.disks.aliyun.key'), config('filesystems.disks.aliyun.secret'))
            ->regionId('eu-central-1')
            ->asDefaultClient();
    }
}
