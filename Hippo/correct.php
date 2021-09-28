<?php

namespace YY\FruitBasket;

use Banana;

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
   * @var array<Fruit> $fruits
   */
  public array $fruits;

  /**
   * A list with fruit origin.
   * @var array<string, string> $origin
   */
  public array $origin;

  /**
   * The maximum number of fruits in the basket.
   * @const MAX_SIZE
   */
  private const MAX_SIZE = 5;

  /**
   * Constructor for class FruitBasket
   */
  public function __construct()
  {
    $this->origin = array(
     'banana' => 'tree',
     'strawberry' => 'plant'
    );

    $this->fruits = array();
  }

  /**
   * Add multiple fruits to the basket.
   *
   * @param array<Fruit> $fruits A list with Fruit instances to add to the basket
   * @throws Exception Throws an exception if the basket does not have enough space.
   */
  public function addFruits( array $fruits ) : void
  {
    $newSize = count( $fruits ) + count( $this->fruits );

    if( $newSize > self::MAX_SIZE )
    {
      throw new Exception( 'Could not add fruits to the basket: not enough space' );
    }

    if( $newSize == self::MAX_SIZE )
    {
      echo 'Warning: basket is getting full!';
    }

    // Loop through all fruits and add them to the basket.
    foreach( $fruits as $Fruit )
    {
      if( !$Fruit instanceof Fruit )
      {
        continue;
      }

      $this->fruits[] = $Fruit;
    }
  }

  /**
   * Add one or more fruits to the basket.
   *
   * @param Fruit ...$fruits One or more fruits to add to the basket
   */
  public function add( Fruit ...$fruits ) : void
  {
    $this->addFruits( $fruits );
  }

  /**
   * Pick a fruit from the basket
   * @param string $type Name of the preferred type of fruit
   * @return array<Fruit>
   */
  public function pickFruit( ?string $type = null ) : array
  {
    if( $this->canPickFruit( $type ) )
    {
      if( $type === null )
      {
        $index = rand( 0, count( $this->fruits ) );
      }
      else
      {
        $preferredFruits = [];
        foreach( $this->fruits as $index => $Fruit )
        {
          if( $Fruit->type === $type )
          {
            $preferredFruits[] = $index;
          }
        }
        $index = $preferredFruits[ rand( 0, count( $preferredFruits ) ) ];
      }

      return array_splice( $this->fruits, $index, 1 );
    }
    return [];
  }

  private function canPickFruit( ?string $type = null ) : bool
  {
    return count( $this->fruits )
      && (
        $type === null
        || $this->containsType( $type )
      );
  }

  private function containsType( string $type ) : bool
  {
    foreach( $this->fruits as $Fruit )
    {
      if( $Fruit->type === $type )
      {
        return true;
      }
    }
    return false;
  }

  /**
   * Checks the state of a banana.
   *
   * @param Banana $Banana The banana to check
   * @return bool Whether the banana is ok or not
   */
  public function checkBanana( Banana $Banana ) : bool
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

  /**
   * Sort the fruits in the basket
   */
  public function sort() : void
  {
    usort(
      $this->fruits,
      function( $FruitA, $FruitB )
      {
        $typeOfFruitA = $FruitA->type;
        $typeOfFruitB = $FruitB->type;
        if( $typeOfFruitA === $typeOfFruitB )
        {
          return 0;
        }

        return $typeOfFruitA < $typeOfFruitB ? -1 : 1;
      }
    );
  }

  /**
   * Get a simple description of the fruit basket.
   * @return string
   */
  public function __toString() : string
  {
    $nrOfFruits = count( $this->fruits );
    return sprintf(
      'There are currently %1$d fruits in the basket. That means you can pick %1$d fruits before the basket is empty.'
      . ' Or you could give %1$d friends one piece of fruit each.',
      $nrOfFruits
    );
  }
}

