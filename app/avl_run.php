<?php

    const _N_MAX_VALUE = 2000;
    const _N_STEP = 10;
    const _AVL_OPERATION_TEST_COUNT = 10000;

    include_once "avl.php";

    function run_test($n) {
        $values = [];
        for ($i=1; $i <= $n; $i++) {
            $values[$i*2] = $i*2;
        }

        $avl = new AVL();
        do {
            $value_key = array_rand($values);
            $avl->insert($values[$value_key]);
            unset($values[$value_key]);
        } while (!empty($values));

        $test_time = 0;
        for ($i = 0; $i < _AVL_OPERATION_TEST_COUNT; $i++) {
            $test_avl = $avl->duplicate();
            $start_time = microtime(true);
            $test_avl->insert(mt_rand(1,$n)*2-1);
            $end_time = microtime(true);
            $test_time += $end_time-$start_time;
            unset($test_avl);
        }
        $avg_insert_time = $test_time/_AVL_OPERATION_TEST_COUNT;

        $test_time = 0;
        for ($i = 0; $i < _AVL_OPERATION_TEST_COUNT; $i++) {
            $test_avl = $avl->duplicate();
            $start_time = microtime(true);
            $test_avl->find(mt_rand(0,$n*2));
            $end_time = microtime(true);
            $test_time += $end_time-$start_time;
            unset($test_avl);
        }
        $avg_find_time = $test_time/_AVL_OPERATION_TEST_COUNT;

        $test_time = 0;
        for ($i = 0; $i < _AVL_OPERATION_TEST_COUNT; $i++) {
            $test_avl = $avl->duplicate();
            $start_time = microtime(true);
            $test_avl->delete(mt_rand(0,$n*2));
            $end_time = microtime(true);
            $test_time += $end_time-$start_time;
            unset($test_avl);
        }
        $avg_delete_time = $test_time/_AVL_OPERATION_TEST_COUNT;

        return [
            "insert" => number_format($avg_insert_time,10,'.',''),
            "find" => number_format($avg_find_time,10,'.',''),
            "delete" => number_format($avg_delete_time,10,'.','')
        ];

        unset($avl);
    }

    $result_file_insert = "n,avg_time\n";
    $result_file_find = "n,avg_time\n";
    $result_file_delete= "n,avg_time\n";

    for ($n = _N_STEP; $n <= _N_MAX_VALUE; $n += _N_STEP) {
        $result_test = run_test($n);
        $result_file_insert .= $n.",".$result_test["insert"]."\n";
        $result_file_find .= $n.",".$result_test["find"]."\n";
        $result_file_delete .= $n.",".$result_test["delete"]."\n";
        echo "N:".$n."  I(s):".$result_test["insert"]." F(s):".$result_test["find"]." D(s):".$result_test["delete"]."\n";
    }

    file_put_contents("./result/avl_insert.csv",$result_file_insert);
    file_put_contents("./result/avl_find.csv",$result_file_find);
    file_put_contents("./result/avl_delete.csv",$result_file_delete);