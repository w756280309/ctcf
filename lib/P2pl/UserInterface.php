<?php

namespace P2pl;

interface UserInterface
{
    public function getUserId();
    public function getLegalName();
    public function getIdcard();
    public function getMobile();
    public function getEpayUserId();
}
