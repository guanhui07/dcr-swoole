<?php

declare(strict_types=1);

namespace App\Service;

use App\Repository\TestRepository;
use App\Repository\UserRepository;
use Carbon\Carbon;
use DcrSwoole\Utils\Parallel;
use Illuminate\Database\Eloquent\Collection;
use itxiao6\SwooleDatabase\Adapter\Manager as DB;

/**
 * Class TestService
 */
class TestService
{
    public TestRepository $testRepository;

    public UserRepository $userRepository;

    public function __construct(TestRepository $testRepository, UserRepository $userRepository)
    {
        $this->testRepository = $testRepository;
        $this->userRepository = $userRepository;
    }

    public function testDi()
    {
        echo 'test Di';
        echo PHP_EOL;
        $this->testRepository->fromRepos();
    }

    public function test($params)
    {
        $this->testRepository
            ->originQuery()
            ->select(['id',])
            ->chunkById(2000, function (Collection $res) {
                $data = $this->userRepository->test(array_column($res->toArray(), 'id'));
                $pointList = $this->userRepository->getQuery()
                    ->whereIn('uid', array_column($data, 'uid'))
                    ->get();
                $dataList = array_column($data, null, 'uid');
                // 协程
                $parallel = new Parallel(20);
                foreach ($pointList as $point) {
                    $parallel->add(function () use ($point, $dataList) {
                        if (!isset($dataList[$point->uid]['income'])) {
                            return;
                        }
                        $expired = $point->point - (int)$dataList[$point->uid]['income'];
                        if ($expired <= 0) {
                            return;
                        }
                        DB::transaction(function () use ($point, $expired) {
                            $point->decrement('point', $expired);
                            $this->userRepository->getQuery()->create([
                                'point' => $expired,
                                'act' => 1,
                                'current_point' => $point->point,
                                'log_time' => Carbon::now()->timestamp
                            ]);
                        });
                    });
                }
                $parallel->wait();
            });
        return [1,2,3];
    }
}
