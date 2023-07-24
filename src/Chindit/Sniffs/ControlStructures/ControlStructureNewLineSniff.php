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
		$ifError = 'Condition keyword must be on a new line';

		if (in_array($tokens[$stackPtr]['code'], [T_IF, T_ELSEIF, T_ELSE], true)) {
			try {
                		$curlyBrace = $tokens[$stackPtr]['scope_opener'];
            		} catch (\Throwable $t) {
                		$phpcsFile->addError('«else if» structures are not allowed', $stackPtr, 'ElseIfOnBraceCheck');
                		return;
            		}
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

		if (in_array($tokens[$stackPtr]['code'], [T_ELSEIF, T_ELSE], true)) {
			$lastBracket = $phpcsFile->findPrevious(T_CLOSE_CURLY_BRACKET, $tokens[$stackPtr]['scope_opener']+1);

			if ($tokens[$lastBracket]['line'] === $tokens[$stackPtr]['line']) {
				$fix = $phpcsFile->addFixableError($ifError, $stackPtr, 'KeywordOnLineAfterBrace');
				if ($fix === true) {
					$phpcsFile->fixer->beginChangeset();

					$phpcsFile->fixer->replaceToken($stackPtr, str_repeat('	', $tokens[$stackPtr]['level']) . $tokens[$stackPtr]['content']);
					$phpcsFile->fixer->addNewline($lastBracket);
					$phpcsFile->fixer->endChangeset();
				}
			}
		}
	}//end process()


}//end class
