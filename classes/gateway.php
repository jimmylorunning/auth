<?php
interface Gateway
{
  public function create($data);
  public function findById($id);
}
