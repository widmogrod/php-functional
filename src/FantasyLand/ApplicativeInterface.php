<?php
namespace FantasyLand;

interface ApplicativeInterface extends ApplyInterface
{
    /**
     * @param callable $b
     * @return self
     */
    public static function of(callable $b);
}