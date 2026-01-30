<?php

namespace Chindit\Sniffs\ControlStructures;

use PHPCSStandards\PHP_CodeSniffer\Files\File;
use PHPCSStandards\PHP_CodeSniffer\Sniffs\Sniff;

class ControlStructureSpaceAroundConditionSniff implements Sniff
{
	/**
	 * Returns an array of tokens this test wants to listen for.
	 *
	 * @return array
	 */
	public function register(): array
	{
		return [
			T_IF,
			T_ELSEIF,
		];

	}//end register()


	/**
	 * Processes this test, when one of its tokens is encountered.
	 *
	 * @param \PHPCSStandards\PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
	 * @param int                         $stackPtr  The position of the current token in the
	 *                                               stack passed in $tokens.
	 *
	 * @return void
	 */
	public function process(File $phpcsFile, int $stackPtr): void
	{
		$tokens = $phpcsFile->getTokens();

		$error = 'If and elseif conditions must have a space before and after parenthese';

		if (in_array($tokens[$stackPtr]['code'], [T_IF, T_ELSEIF], true))
		{
			if ($tokens[ $stackPtr + 1 ]['code'] !== T_WHITESPACE) {
				$fix = $phpcsFile->addFixableError($error, $stackPtr, 'SpaceBeforeConditionalParentheses');
				if ($fix === true) {
					$phpcsFile->fixer->beginChangeset();
					$phpcsFile->fixer->addContentBefore($stackPtr+1, ' ');
					$phpcsFile->fixer->endChangeset();
				}
			}

			if ($tokens[ $tokens[ $stackPtr ][ 'parenthesis_closer' ] + 1 ]['code'] !== T_WHITESPACE) {
				$fix = $phpcsFile->addFixableError($error, $stackPtr, 'SpaceAfterConditionalParentheses');
				if ($fix === true) {
					$phpcsFile->fixer->beginChangeset();
					$phpcsFile->fixer->addContentBefore($tokens[ $stackPtr ][ 'parenthesis_closer' ] + 1, ' ');
					$phpcsFile->fixer->endChangeset();
				}
			}
		}
	}//end process()
}
