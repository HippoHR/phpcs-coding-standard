<?php
/**
 * Unit test class for the ClassDefinitionOpeningBraceSpace sniff.
 *
 * @author Dennis Claassen <claassen@uitzendbureau.nl>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace Hippo\Tests\CSS;

use PHP_CodeSniffer\Tests\Standards\AbstractSniffUnitTest;

class ClassDefinitionOpeningBraceSpaceUnitTest extends AbstractSniffUnitTest
{
  /**
   * Returns the lines where errors should occur.
   *
   * The key of the array should represent the line number and the value
   * should represent the number of errors that should occur on that line.
   *
   * @return array<int, int>
   */
  protected function getErrorList() : array
  {
    return [
      1 => 1,
      5 => 1,
      19 => 2,
      21 => 1,
      23 => 1,
      31 => 1,
      33 => 0,
      44 => 1,
      50 => 1,
      61 => 1,
      71 => 1,
      76 => 1,
    ];
  }

  /**
   * Returns the lines where warnings should occur.
   *
   * The key of the array should represent the line number and the value
   * should represent the number of warnings that should occur on that line.
   *
   * @return array<int, int>
   */
  protected function getWarningList() : array
  {
    return [];
  }
}
