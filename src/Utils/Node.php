<?php
namespace App\Utils;

class Node {
    public mixed $edges;

    public function __construct(private mixed $value) {
        $this->value = $value;
        $this->edges = [];
    }

    public function addEdge(mixed $neighbor, mixed $weight): void
    {
        $this->edges[] = ['neighbor' => $neighbor, 'weight' => $weight];
    }
}