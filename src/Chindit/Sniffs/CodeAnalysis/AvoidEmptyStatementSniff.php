<?php

namespace Chindit\Sniffs\CodeAnalysis;


use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

class AvoidEmptyStatementSniff implements Sniff
{
	public function register(): array
	{
		return [
			T_SEMICOLON
		];
	}

	public function process(File $phpcsFile, int $stackPtr): void
	{
		$tokens = $phpcsFile->getTokens();

		if ($tokens[$stackPtr - 1]['code'] === T_SEMICOLON || ($tokens[$stackPtr - 2]['code'] === T_SEMICOLON && $tokens[$stackPtr -1]['code'] === T_WHITESPACE))
		{
			$error = 'Empty statement found';
			$fix = $phpcsFile->addFixableError($error, $stackPtr, 'EmptyStatement');
			if ($fix === true) {
				$phpcsFile->fixer->beginChangeset();
				$phpcsFile->fixer->replaceToken($stackPtr, '');
				$phpcsFile->fixer->endChangeset();
			}
		}
	}
}
