<?php

namespace YY\FruitBasket;

use Banana;
use Exception;

/**
 * Manages a fruit basket. This is a very long line. A very, very, very, very long line =) We should cut off the line if the limit is reached. We should be
 * warned by PHPCodeSniffer and Sonar when a single line is longer than 160 characters.
 *
 * @package YY
 * @subpackage Fruit
 */
class FruitBasket
{
  /**
   * The list with fruits in the basket.
   * @var array $fruits
   */
  public $fruits;

  /**
   * A list with fruit origin.
   * @var array $origin
   */
  public $origin;

  /**
   * The maximum number of fruits in the basket.
   * @const MAX_SIZE
   */
  const MAX_SIZE = 5;

  /**
   * Constructor for class FruitBasket
   */
  public function __construct()
  {
    $this->origin = new array(
     'banana' => 'tree' . "\n\t\r\v\e\f\\\$\"\0\00\000\xA9\xFF\xa9\xff",
     'strawberry' => 'plant'
    );

    $this->fruits = new array();
  }

  /**
   * Add multiple fruits to the basket.
   *
   * @param array $fruits A list with Fruit instances to add to the basket
   * @throws Exception Throws an exception if the basket does not have enough space.
   */
  public function addFruits( $fruits )
  {
    $this->checkBanana( $fruits[ 0 ] );

    $TestObject->method();
    $TestObject[] = 0;

    $newSize = count( $fruits ) + count( $this->fruits );

    if( $newSize > self::MAX_SIZE )
    {
      throw new Exception( 'Could not add fruits to the basket: not enough space' );
    }
    elseif( $newSize == self::MAX_SIZE )
    {
      echo 'Warning: basket is getting full!';
    }

    // Loop through all fruits and add them to the basket.
    foreach( $fruits as $Fruit )
    {
      $this->fruits[] = $Fruit;
    }

    // Lets see if the fruit objects are in the array $this->fruits
    var_dump( $this->fruits );
    print_r( $this->fruits );
  }

  /**
   * Checks the state of a banana.
   *
   * @param Banana $Banana The bana to check
   * @return boolean Whether the banana is ok or not
   */
  public function checkBanana( $Banana )
  {
    // Check banana color.
    switch( $Banana->color )
    {
      case 'green':
        echo 'green' . "\n";
        // Intentional no break
      case 'yellowish green':
        echo 'yellowish green' . "\n";
        echo 'Banana is not yet ripe.';
        break;
      case 'brown':
        echo 'brown' . "\n";
        echo 'Banana is rotten.';
        break;
      case 'orange':
        return false;
      default:
        return true;
    }
    return false;
  }
}
