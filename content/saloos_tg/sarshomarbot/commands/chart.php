<?php
namespace content\saloos_tg\sarshomarbot\commands;

class chart
{
  public static function calc_vertical($_datalist)
  {
    $row      = ['0⃣', '1⃣', '2⃣', '3⃣', '4⃣', '5⃣', '6⃣', '7⃣', '8⃣', '9⃣', '🔟'];
    $chart    = "";
    $max      = 10;
    $devider  = 100 / $max;
    $total    = null;

    ksort($_datalist);
    $datalist = self::calc_chart($_datalist, true, true);
    for ($i=0; $i < $max; $i++)
    {
      $chart_row = "";
      foreach ($datalist as $key => $value)
      {
        if($i === 0)
        {
          if(isset($row[$key]))
          {
            $chart_row .= $row[$key];
          }
          else
          {
            $chart_row .= $key;
          }
        }
        else
        {
          $fill         = $value / $devider;
          $fill_divided = $fill - $i +1;

          // empty or full
          if($fill_divided > 0)
          {
            // if this row is full
            if($fill_divided >= 1.0)
            {
              $chart_row .= "⬛️";
            }
            // if more than half
            elseif($fill_divided >= 0.5)
            {
              $chart_row .= '🔲';
            }
            // if less than half
            else
            {
              $chart_row .= '🔳';
            }
          }
          // if empty
          else
          {
            $chart_row .= "⬜️";
          }
        }
      }

      $chart = $chart_row."\n". $chart;
    }
    // add total of rows into chart first row
    if($total)
    {
      $chart = "جزئیات $total کارت مرورشده\n". $chart;
    }
    return $chart;
  }
  public static function calc_chart($_inputList, $_showtext = true, $_onlyArray = false)
  {
    $result  = "";
    $shape   = "🔷";
    $total   = array_sum($_inputList);
    $divider = 15;

    foreach ($_inputList as $key => $value)
    {
      $key_new              = $key.'P';
      $_inputList[$key_new] = $value * 100 / $total;
      $_inputList[$key_new] = round($_inputList[$key_new], 1);
      $_inputList[$key.'C'] = ceil($_inputList[$key_new] / $divider);

      // add prefix
      if($_showtext)
      {
        $result .= "`[";
        if(is_string($_showtext))
        {
          $result .= $_showtext;
        }
        $result .= $key. "] ". str_pad($value, 3). "` ";
      }

      $result .= str_repeat($shape, $_inputList[$key.'C']);
      $result .= "\n";

      if($_onlyArray)
      {
        $_inputList[$key] = (int)ceil($_inputList[$key_new]);
        unset($_inputList[$key_new]);
        unset($_inputList[$key.'C']);
      }
    }

    if($_onlyArray)
    {
      return $_inputList;
    }
    return $result;
  }
}
?>