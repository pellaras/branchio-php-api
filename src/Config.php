<?php

namespace BranchIo;

/**
 * Class Config
 *
 * @author Nikolay Ivlev <nikolay.kotovsky@gmail.com>
 */
class Config
{
    /**
     * @var string
     */
    protected $branchKey;

    /**
     * @var string
     */
    protected $branchSecret;

    /**
     * @return string
     */
    public function getBranchKey()
    {
        return $this->branchKey;
    }

    /**
     * @param string $branchKey
     */
    public function setBranchKey($branchKey)
    {
        $this->branchKey = $branchKey;
    }

    /**
     * @return string
     */
    public function getBranchSecret()
    {
        return $this->branchSecret;
    }

    /**
     * @param string $branchSecret
     */
    public function setBranchSecret($branchSecret)
    {
        $this->branchSecret = $branchSecret;
    }
}
