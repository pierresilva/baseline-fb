<?php


namespace App\Console\Commands\CrudScaffold;


class StubCompiler
{
  private $debugFlag = false;

  private $stubTxt;
  private $stubArray;
  private $rootVars;

  public function __construct($stubTxt, $rootVars)
  {
    $this->stubTxt = $stubTxt;
    $this->rootVars = $rootVars;
    $this->stubArray = [];
  }

  public function compile()
  {

    if ($this->debugFlag) {
      echo('[func]compile' . "\n");
    }
    $result = '';

    //delete return before tag
    $this->stubTxt = str_replace(array("}}}\r\n{{{", "}}}\r{{{", "}}}\n{{{"), "}}}{{{", $this->stubTxt);
    $this->stubTxt = str_replace(array("}}}\r\n", "}}}\r", "}}}\n"), "}}}", $this->stubTxt);

    /* prepare - parse */
    $pattern_tag = '#(\{\{\{ [^}]* \}\}\})#';
    $this->stubArray = preg_split($pattern_tag, $this->stubTxt, null, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);  // {{{ xxxxx }}}

    //compile
    $result = $this->compileLoop($this->stubArray, '');

    return $result;
  }


  private function compileLoop($localStubArray, $thisPath)
  {

    //if($this->debugFlag){ echo("\n".'[func]compileLoop'."\n"); }
    if ($this->debugFlag) {
      echo("\n" . "-----" . '[func]compileLoop start -----' . "\n");
    }
    $result = '';

    $beforeTag = '';   //if, elseif, endif, foreach, endforeach // use for parse check
    $parentTag = '';   //if, foreach //currently under depth-0 [if or foreach] or not
    $newLocalStubArray = []; //for second depth
    $depth = 0;
    $ifCondition = -1; //0:false condition, 1:true condition, 2:skip condition
    $var_foreach = '';

    $patternVar = '#\{\{\{ \$([^\|\}]*)\|?([^\}]*) \}\}\}#';
    $patternIf = '#\{\{\{ if\(\$([^=]*)==(.*?)\): \}\}\}#';
    $patternIfnot = '#\{\{\{ if\(\$([^=]*)!=(.*?)\): \}\}\}#';
    $patternElseif = '#\{\{\{ elseif\(\$([^=]*)==(.*?)\): \}\}\}#';
    $patternElseifnot = '#\{\{\{ elseif\(\$([^=]*)!=(.*?)\): \}\}\}#';
    $patternForeach = '#\{\{\{ foreach\(\$(.*?)\): \}\}\}#';

    for ($i = 0; $i < count($localStubArray); $i++) {

      //var
      if (preg_match($patternVar, $localStubArray[$i], $m)) {

        //if($this->debugFlag){ echo ('[depth:'.$depth.']var : ['.implode(',',$m)."]"."\n"); }

        if ($depth > 0) {
          $newLocalStubArray[] = $localStubArray[$i];
        } else {
          $varPath = $m[1];
          $pipe = $m[2];
          $result .= $this->compileVar($varPath, $pipe, $thisPath);

          if ($this->debugFlag) {
            echo($localStubArray[$i] . ' ---> ' . $this->compileVar($varPath, $pipe, $thisPath) . "\n");
          }
        }

        //if
      } elseif (preg_match($patternIf, $localStubArray[$i], $m) || preg_match($patternIfnot, $localStubArray[$i], $m)) {

        //if($this->debugFlag){ echo ('[depth:'.$depth.']if : ['.implode(',',$m)."]"."\n"); }

        if ($depth > 0) {
          $newLocalStubArray[] = $localStubArray[$i];
        } else {
          $varPath = $m[1];
          $targetStr = $m[2];
          if (strpos($localStubArray[$i], '!=')) {
            $ifCondition = $this->checkIfCondition($varPath, $targetStr, $thisPath, $reverse = true);
          } else {
            $ifCondition = $this->checkIfCondition($varPath, $targetStr, $thisPath, $reverse = false);
          }
          $beforeTag = 'if';
          $newLocalStubArray = [];

          if ($ifCondition == 1) {
            if ($this->debugFlag) {
              echo($localStubArray[$i] . ' ---> OK!' . "\n");
            }
          } else {
            if ($this->debugFlag) {
              echo($localStubArray[$i] . ' ---> NG!' . "\n");
            }
          }
        }
        $depth += 1;

        //elseif
      } elseif (preg_match($patternElseif, $localStubArray[$i], $m) || preg_match($patternElseifnot, $localStubArray[$i], $m)) {

        //if($this->debugFlag){ echo ('[depth:'.$depth.']elseif : ['.implode(',',$m)."]"."\n"); }

        if ($depth > 1) {
          $newLocalStubArray[] = $localStubArray[$i];
        } else {

          //parse check
          if ($beforeTag == 'foreach' || $beforeTag == '') {
            throw new \Exception("Stub parse error!");
          }

          if ($ifCondition == 1) {
            //evaluate $newLocalStubArray
            $result .= $this->compileLoop($newLocalStubArray, $thisPath);
            $ifCondition = 2; // change to skip mode
          }

          if ($ifCondition != 2) {

            $varPath = $m[1];
            $targetStr = $m[2];

            if (strpos($localStubArray[$i], '!=')) {
              $ifCondition = $this->checkIfCondition($varPath, $targetStr, $thisPath, $reverse = true);
            } else {
              $ifCondition = $this->checkIfCondition($varPath, $targetStr, $thisPath, $reverse = false);
            }

            if ($ifCondition == 1) {
              if ($this->debugFlag) {
                echo($localStubArray[$i] . ' ---> OK!' . "\n");
              }
            } else {
              if ($this->debugFlag) {
                echo($localStubArray[$i] . ' ---> NG!' . "\n");
              }
            }
          } else {
            if ($this->debugFlag) {
              echo($localStubArray[$i] . ' ---> SKIP!' . "\n");
            }
          }
          $beforeTag = 'elseif';
          $newLocalStubArray = [];
        }

        //else
      } elseif ($localStubArray[$i] == '{{{ else: }}}') {

        //if($this->debugFlag){ echo ('[depth:'.$depth.']else : ['.implode(',',$m)."]"."\n"); }

        if ($depth > 1) {
          $newLocalStubArray[] = $localStubArray[$i];
        } else {

          //parse check
          if ($beforeTag == 'foreach') {
            throw new \Exception("Stub parse error!");
          }

          if ($ifCondition == 1) {
            //evaluate $newLocalStubArray
            $result .= $this->compileLoop($newLocalStubArray, $thisPath);
            $ifCondition = 2; // change to skip mode
          }

          if ($ifCondition !== 2) {
            $ifCondition = 1;

            if ($this->debugFlag) {
              echo($localStubArray[$i] . ' ---> OK!' . "\n");
            }
          } else {
            if ($this->debugFlag) {
              echo($localStubArray[$i] . ' ---> SKIP!' . "\n");
            }
          }
          $beforeTag = 'else';
          $newLocalStubArray = [];
        }

        //endif
      } elseif ($localStubArray[$i] == '{{{ endif; }}}') {

        //if($this->debugFlag){ echo ('[depth:'.$depth.']endif'."\n"); }

        if ($depth > 1) {
          $newLocalStubArray[] = $localStubArray[$i];
        } else {

          //parse check
          if ($beforeTag == 'foreach') {
            throw new \Exception("Stub parse error!");
          }

          if ($ifCondition == 1) {

            //evaluate $newLocalStubArray
            $result .= $this->compileLoop($newLocalStubArray, $thisPath);
          }

          $ifCondition = -1; // change to default
          $parentTag = '';
          $beforeTag = 'endif';
          $newLocalStubArray = [];

          if ($this->debugFlag) {
            echo($localStubArray[$i] . "\n");
          }
        }
        $depth -= 1;

        //foreach
      } elseif (preg_match($patternForeach, $localStubArray[$i], $m)) {

        if ($this->debugFlag) {
          echo('[depth:' . $depth . ']foreach : [' . implode(',', $m) . "]" . "\n");
        }

        if ($depth > 0) {
          $newLocalStubArray[] = $localStubArray[$i];
        } else {
          $parentTag = 'foreach';
          $beforeTag = 'foreach';
          $varPathForeach = $m[1];
          $newLocalStubArray = [];
        }
        $depth += 1;

        //endforeach
      } elseif ($localStubArray[$i] == '{{{ endforeach; }}}') {

        if ($this->debugFlag) {
          echo('[' . $depth . ']endforeach' . "\n");
        }

        if ($depth > 1) {
          $newLocalStubArray[] = $localStubArray[$i];
        } else {

          //parse check
          if ($beforeTag == 'if' || $beforeTag == 'elseif') {
            throw new \Exception("Stub parse error!");
          }

          $varPathForeach = $this->mergePath($varPathForeach, $thisPath);
          $loop_array = $this->dataGet($this->rootVars, $varPathForeach);
          //array check
          if (!is_array($loop_array)) {
            throw new \Exception("var for foreach is not array!");
          }
          for ($j = 0; $j < count($loop_array); $j++) {
            $result .= $this->compileLoop($newLocalStubArray, $varPathForeach . '.' . $j);
          }
          $parentTag = '';
          $beforeTag = 'endforeach';
          $newLocalStubArray = [];
        }
        $depth -= 1;

        //code
      } else {
        //if($this->debugFlag){ echo ('['.$depth.']code:'.$localStubArray[$i]."\n"); }

        if ($depth > 0) {
          $newLocalStubArray[] = $localStubArray[$i];
        } else {
          if ($this->debugFlag) {
            echo($localStubArray[$i]);
          }
          $result .= $localStubArray[$i];
        }
      }
    }

    //parse check
    if ($beforeTag == 'foreach' || $beforeTag == 'if' || $beforeTag == 'elseif') {
      throw new \Exception("Stub parse error!");
    }

    if ($this->debugFlag) {
      echo("-----" . '[func]compileLoop end -----' . "\n\n");
    }

    return $result;
  }


  // return string
  private function compileVar($varPath, $pipe, $thisPath)
  {

    $varPath = $this->mergePath($varPath, $thisPath);
    $result = $this->dataGet($this->rootVars, $varPath);

    if ($pipe !== '') {
      return NameResolver::solveName($result, $pipe);
    }
    return $result;
  }

  private function mergePath($varPath, $thisPath)
  {
    $varPathArray = explode('.', $varPath);
    $thisPath_array = explode('.', $thisPath);

    if ($varPathArray[0] == 'this') {
      $varPathArray = array_merge($thisPath_array, array_slice($varPathArray, 1));
    } elseif ($varPathArray[0] == 'parent') {
      $varPathArray = array_merge(array_slice($thisPath_array, 0, -2), array_slice($varPathArray, 1));
    }
    $varPath = implode('.', $varPathArray);
    return $varPath;
  }

  private function checkIfCondition($varPath, $targetStr, $thisPath, $reverse = false)
  {

    $var01 = $this->compileVar($varPath, '', $thisPath);

    // case of null array check
    if ($targetStr === '[]') {
      $targetStr = [];
    } elseif ($targetStr === 'true') {
      $targetStr = true;
    } elseif ($targetStr === 'false') {
      $targetStr = false;
    } elseif (!preg_match("#^'(.*)'$#", $targetStr)) {
      throw new \Exception('if target var must be [] or true or false or string');
    } elseif (preg_match("#^'(.*)'$#", $targetStr, $m)) {
      $targetStr = $m[1];
    }

    if ($reverse) {
      if ($var01 === $targetStr) {
        return 0;
      } else {
        return 1;
      }
    } else {
      if ($var01 === $targetStr) {
        return 1;
      } else {
        return 0;
      }
    }
  }

  private function dataGet($data, $keys)
  {

    $keysArray = explode('.', $keys);

    $current = $data;
    foreach ($keysArray as $key) {
      if (is_array($current)) {
        if (!array_key_exists($key, $current)) {
          throw new \Exception('$current doesn\'t has key:' . $key);
        }
        $current = $current[$key];
      } elseif (is_object($current)) {

        if (mb_substr($key, -2) === '()') {   // case - $key is method

          $key2 = rtrim($key, '()');
          if (!method_exists($current, $key2)) {
            throw new \Exception(get_class($current) . ' doesn\'t has method:' . $key);
          }
          $current = $current->$key2();

        } else {  // case - $key is property

          if (!property_exists($current, $key)) {
            throw new \Exception(get_class($current) . ' doesn\'t has property:' . $key);
          }
          $current = $current->$key;
        }
      } else {
        throw new \Exception('$current(' . $current . ') is not array or object');
      }
    }
    return $current;
  }

}
