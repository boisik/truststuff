<?php
ini_set('max_execution_time', 6);
/**
 * Created by PhpStorm.
 * User: alink
 * Date: 20.08.2020
 * Time: 13:38
 */



class AnnaTs
{
    public
        $result = array(),
        $fileName ='result.txt';
    function init()
    {
        $array = array(
            '0'=>array('from'=>'9000000000','to'=>'9000061999'),
            '1'=>array('from'=>'9000062000','to'=>'9000062999'),
            '2'=>array('from'=>'9000063000','to'=>'9000099999'),
            '3'=>array('from'=>'9000100000','to'=>'9000199999'),
            '4'=>array('from'=>'9000200000','to'=>'9000299999'),
            '5'=>array('from'=>'9000300000','to'=>'9000499999'),
            '6'=>array('from'=>'9000500000','to'=>'9000599999'),
            '7'=>array('from'=>'9000600000','to'=>'9000999999'),
            '8'=>array('from'=>'9001000000','to'=>'9001099999'),
            '9'=>array('from'=>'9001100000','to'=>'9001199999'),
            '10'=>array('from'=>'9001200000','to'=>'9001399999'),
            '11'=>array('from'=>'9001400000','to'=>'9001899999'),
            '12'=>array('from'=>'9001900000','to'=>'9001909999'),
            '13'=>array('from'=>'9001910000','to'=>'9001919999'),
            '14'=>array('from'=>'9001920000','to'=>'9001929999'),
            '15'=>array('from'=>'9001930000','to'=>'9001939999'),
            '16'=>array('from'=>'9001940000','to'=>'9001949999'),
            '17'=>array('from'=>'9001950000','to'=>'9001959999'),
            '18'=>array('from'=>'9001960000','to'=>'9001969999'),
           '19'=>array('from'=>'9001970000','to'=>'9002169999'),
            '20'=>array('from'=>'9002170000','to'=>'9002187999'),
        );

        /**
         * Отделение общей части для элементов диапазона
         *
         *
         */
        $okfrom = array();
        $okto = array();
        $work = array();
        foreach ($array as $key=>$one){
            $first = str_split($one['from']);
            $second = str_split($one['to']);

            $this->result[] = array($one['from'],$one['to']);
            foreach ($first as $number => $char){

                if($first[$number] == $second[$number]){
                    $okfrom[$key]=$okfrom[$key].$one['from'][$number];
                    $okto[$key]=$okto[$key].$one['to'][$number];

                }elseif($second[$number]!='9' && $first[$number] != $second[$number]){
                    $work[$key]['from']=$work[$key]['from'].$first[$number];
                    $work[$key]['to']=$work[$key]['to'].$second[$number];

                }elseif ($second[$number]=='9' && $first[$number] ='0' && $first[$number-1] == $second[$number-1] ){
                    $work[$key]['from']='stability';
                    $work[$key]['to']='stability';
                }
                elseif ($second[$number]=='9' && $first[$number]!='9' && $char!='0' ){

                    $work[$key]['from']=$work[$key]['from'].$char;
                    $work[$key]['to']=$work[$key]['to'].$second[$number];
                }


            }

            /**
             * Запуск итеративной генерации элементов диапазона
             */
            $this->iterrate($work[$key]['from'],$work[$key]['to'],$this->result,$okfrom[$key]);

        }

        /**
         * убрать лишьне нули
         */
        foreach ($this->result as $key=>$line){

            if (substr($line['0'], -1)=='0' && substr($line['0'], 0,1)=='+'){

                if (substr($this->result[$key+1]['0'], -1)=='0'){

                    $line['0'] = substr($line['0'], 0, -1);


                    $this->result[$key]['0'] =$line['0'];
                }
            }
        }

    }

    function iterrate($workKeyFrom,$workKeyTo,$result,$okFromKey)
    {
        /**
         * отработка вариантов выхода и рекурсии
         */

        if($workKeyFrom=='stability'){
            $result[] = array($okFromKey,null);
            $this->result = $result;
            return 1;
        }

        $result[] = array('+7'.$okFromKey.$workKeyFrom,null);

        if($workKeyFrom==$workKeyTo){

            $this->result = $result;
            return 1;
        }

        if(strlen($workKeyFrom)>1 && substr($workKeyTo, -1)=='9'){

            $workArray = str_split($workKeyFrom);
            $workArray = array_reverse($workArray);
            $workKeyTo =str_split($workKeyTo);
            $workKeyTo = array_reverse($workKeyTo);

            $i=1;
            $return =true;
            foreach ($workKeyTo as $stepKey => $step){
                if ($i==1 && $step!=9) $return = false;
                if ($workKeyTo[$stepKey] != $workArray[$stepKey] && $i!=1)$return = false;
                $i++;
            }
            if ($return){
                $this->result = $result;
                return 1;
            }

            $workKeyTo = array_reverse($workKeyTo);
            $workKeyTo = implode('',$workKeyTo);


        }



        /**
         * непосрественная генерация элементов
         */

        $workArray = str_split($workKeyFrom);
        $workArray = array_reverse($workArray);

        $workKeyTo =str_split($workKeyTo);
        $workKeyTo = array_reverse($workKeyTo);
        $number = 0;
        $char = $workArray[$number];



        if ($char == 0){

            if($workArray[$number+1] == $workKeyTo[$number+1]){
                $workArray[$number] = $workArray[$number]+1;
                $workArray = array_reverse($workArray);
                $workKeyFrom = implode('',$workArray);

            }else{
                $workArray[$number+1] = $workArray[$number+1]+1;
                $workArray = array_reverse($workArray);
                $workKeyFrom = implode('',$workArray);
            }


        }
        if ($char == 9){
            $workArray[$number+1] = $workArray[$number+1]+1;
            $workArray[$number] = 0;

            if($workArray[$number+1] == '10'){
                $workArray[$number+2] =$workArray[$number+2]+1;
                $workArray[$number+1] =0;
            }

            $workArray = array_reverse($workArray);
            $workKeyFrom = implode('',$workArray);

        }
        if ($char != 9 && $char != 0){

            $workArray[$number] = $workArray[$number]+1;
            $workArray = array_reverse($workArray);
            $workKeyFrom = implode('',$workArray);
        }

        $workKeyTo = array_reverse($workKeyTo);
        $workKeyTo = implode('',$workKeyTo);



        $this->iterrate($workKeyFrom,$workKeyTo,$result,$okFromKey);
    }

    /**
     * Запись результата в файл
     */
    function writeToFile()
    {
     foreach ($this->result as $line){
         foreach ($line as $part){
             file_put_contents($this->fileName, $part.'    ',FILE_APPEND);
         }
         file_put_contents($this->fileName,PHP_EOL,FILE_APPEND);
     }
    }
}
$class = new AnnaTs();
$class->init();
$class->writeToFile();