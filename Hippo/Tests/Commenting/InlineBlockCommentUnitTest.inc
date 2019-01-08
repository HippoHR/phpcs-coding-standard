<?php

/* Comments need to contain a single space on both sides */

/*They can't have no spaces on either side*/

/*  They can't have multiple spaces on either side  */

/** Doc comments also need to contain a single space on both sides */

/**They also can't have no spaces on either side*/

/**  And they too can't have multiple spaces on either side  */

/* Missing a space at the end*/
/*Missing a space at the start */
/** Missing a space at the end*/
/**Missing a space at the start */

/*This should not report
  for multiline comments*/

/**This should not report
  for multiline doc comments*/

//This should be ignored since its not a block comment
