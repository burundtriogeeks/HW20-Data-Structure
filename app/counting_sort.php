<?php

    const _N_MAX_VALUE = 20000;
    const _N_STEP = 10;
    const _SORT_TEST_COUNT = 100;

    function createTestArray($n) {
        $result = [];
        for ($i = 0; $i < $n; $i++) {
            $result[] = mt_rand(0,$n/2);
        }
        return $result;
    }

    function counting_sort($input) {
        $init_memory = memory_get_usage();
        $start_time = microtime(true);

        $max_value = max($input);

        $aux = array_fill(0,$max_value+1,0);

        foreach ($input as $i) {
            $aux[$i]++;
        }
        $prev = 0;
        for($i = 0; $i <= $max_value+1; $i++) {
            $aux[$i] = $prev + $aux[$i];
            $prev = $aux[$i];
        }
        $sortedArr = array_fill(0,count($input),0);

        foreach ($input as $i) {
            $sortedArr[$aux[$i]-1] = $i;
            $aux[$i]--;
        }


        $end_time = microtime(true);
        $final_memory = memory_get_usage();
        return [
            "memory" => $final_memory-$init_memory,
            "time" => $end_time-$start_time,
            "result" => $sortedArr
        ];
    }

    $result_file_time = "n,avg_time\n";
    $result_file_memory = "n,avg_memory\n";

    for ($n = _N_STEP; $n <= _N_MAX_VALUE; $n += _N_STEP) {

        $avg_time = 0;
        $avg_memory = 0;

        for ($j = 0; $j < _SORT_TEST_COUNT; $j++) {
            $input = createTestArray($n);
            $res = counting_sort($input);

            $avg_time += $res["time"];
            $avg_memory += $res["memory"];
        }

        $result_file_time .= $n . "," . number_format($avg_time / _SORT_TEST_COUNT, 10, '.', '') . "\n";
        $result_file_memory .= $n . "," . round($avg_memory / _SORT_TEST_COUNT) . "\n";

        echo "N:" . $n . " T(s):" . number_format($avg_time / _SORT_TEST_COUNT, 10, '.', '') . " M(b):" . round($avg_memory / _SORT_TEST_COUNT) . "\n";

    }

    file_put_contents("./result/csort_time.csv",$result_file_time);
    file_put_contents("./result/csort_memory.csv",$result_file_memory);

