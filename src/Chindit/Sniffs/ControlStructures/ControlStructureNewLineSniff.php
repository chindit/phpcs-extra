<?php
/**
 * Verifies that all braces for if, elseif and else are on a new line
 *
 * @author    David Lumaye <littletiger58@gmail.com>
 * @copyleft  2020
 * @license   https://github.com/chindit/phpcs-extra/blob/master/licence.txt GNU GPL v3.0 Licence
 */

namespace Chindit\Sniffs\ControlStructures;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

class ControlStructureNewLineSniff implements Sniff
{


	/**
	 * Returns an array of tokens this test wants to listen for.
	 *
	 * @return array
	 */
	public function register()
	{
		return [
			T_IF,
			T_ELSE,
			T_ELSEIF,
		];

	}//end register()


	/**
	 * Processes this test, when one of its tokens is encountered.
	 *
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
	 * @param int                         $stackPtr  The position of the current token in the
	 *                                               stack passed in $tokens.
	 *
	 * @return void
	 */
	public function process(File $phpcsFile, $stackPtr)
	{
		$tokens = $phpcsFile->getTokens();

		$error = 'Brace must be on a new line';

		if (in_array($tokens[$stackPtr]['code'], [T_IF, T_ELSEIF, T_ELSE], true)) {
			$curlyBrace  = $tokens[$stackPtr]['scope_opener'];
			$lastContent = $phpcsFile->findPrevious(T_WHITESPACE, ($curlyBrace - 1), $stackPtr, true);
			$classLine   = $tokens[$lastContent]['line'];
			$braceLine   = $tokens[$curlyBrace]['line'];
			if ($classLine !== ($braceLine-1)) {
				$fix = $phpcsFile->addFixableError($error, $stackPtr, 'BraceOnNewLine');
				if ($fix === true) {
					$phpcsFile->fixer->beginChangeset();
					if ($tokens[($curlyBrace - 1)]['code'] === T_WHITESPACE) {
						$phpcsFile->fixer->replaceToken(($curlyBrace - 1), '');
					}

					$phpcsFile->fixer->replaceToken($curlyBrace, str_repeat('	', $tokens[$stackPtr]['level']) . '{');
					$phpcsFile->fixer->addNewlineBefore($curlyBrace);
					$phpcsFile->fixer->endChangeset();
				}
			}
		}
	}//end process()


}//end class
