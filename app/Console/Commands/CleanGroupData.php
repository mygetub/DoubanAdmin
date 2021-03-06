<?php

namespace App\Console\Commands;

use App\Group;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CleanGroupData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'douban:clean-group-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '清理过期的豆瓣租房信息';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //删除关联记录
        $data = Group::where("updated_at", '<=', date("Y-m-d H:i:s", strtotime("-10 days")))->get();
        foreach ($data as $r) {
            $groupMark = $r->groupMark;
            foreach ($groupMark as $gm) {
                $gm->where(['url' => $gm->url, 'user_id' => $gm->user_id, 'type' => $gm->type])->delete();
            }
        }

        $result = Group::where("updated_at", '<=', date("Y-m-d H:i:s", strtotime("-10 days")))->delete();
        if ($result || $result === 0) {
            $msg = date("Y-m-d H:i:s") . " Clean group data finished";
            echo $msg . PHP_EOL;
            Log::info($msg);
            return true;
        } else {
            $msg = date("Y-m-d H:i:s") . " Clean group data failed";
            echo $msg . PHP_EOL;
            Log::info($msg);
            return false;
        }
    }
}
