<?php

/**
 * Description of ServiceInterface
 *
 * @author: Awoyo Oluwatoyin Stephen alias awoyotoyin <awoyotoyin@gmail.com>
 */
namespace Zfe\Common\Service;

use Common\Provider\ProviderInterface;

interface ServiceInterface
{
    public function index();

    public function read($id);

    public function save(array $data);

    public function delete($id);
}
