<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Cityware\Security;

/**
 * Description of Rsa
 *
 * @author fabricio.valadares
 */
class Rsa
{
    /**
     * Function for generating keys. Return array where
     * $array['module'] -> modulo N
     * $array['public'] -> public key E
     * $array['private'] -> private key D
     *
     * Public key pair is N and E
     * Private key pair is N and D
     *
     * @param  integer $p
     * @param  integer $q
     * @param  boolean $show_debug
     * @return array
     */
    public function generateKeys($p = null, $q = null, $show_debug = 0)
    {
        $p = (!empty($p)) ? $p : (int) gmp_strval(gmp_nextprime(mt_rand(0, 10000)));
        $q = (!empty($q)) ? $q : (int) gmp_strval(gmp_nextprime(mt_rand(0, 10000)));

        $n = bcmul($p, $q);

        //m (we need it to calculate D and E)
        $m = bcmul(bcsub($p, 1), bcsub($q, 1));

        // Public key  E
        $e = $this->findE($m);

        // Private key D
        $d = $this->extend($e, $m);

        $keys = array('module' => $n, 'public' => $e, 'private' => $d);
        if ($show_debug) {
            echo "P = $p<br />
                  Q = $q<br />
                  <b>N = $n</b> - modulo<br />
                  M = $m<br />
                  <b>E = $e</b> - public key<br />
                  <b>D = $d</b> - private key<p>";
        }

        return $keys;
    }

    /**
     * Standard method of calculating D
     * D = E-1 (mod N)
     * It's presumed D will be found in less then 16 iterations
     *
     * @param  integer $Ee
     * @param  integer $Em
     * @return string
     */
    private function extend($Ee, $Em)
    {
        $u1 = '1';
        $u2 = '0';
        $u3 = $Em;
        $v1 = '0';
        $v2 = '1';
        $v3 = $Ee;
        while (bccomp($v3, 0) != 0) {
            $qq = bcdiv($u3, $v3, 0);
            $t1 = bcsub($u1, bcmul($qq, $v1));
            $t2 = bcsub($u2, bcmul($qq, $v2));
            $t3 = bcsub($u3, bcmul($qq, $v3));
            $u1 = $v1;
            $u2 = $v2;
            $u3 = $v3;
            $v1 = $t1;
            $v2 = $t2;
            $v3 = $t3;
            $z = '1';
        }
        $uu = $u1;
        $vv = $u2;
        if (bccomp($vv, 0) == -1) {
            $inverse = bcadd($vv, $Em);
        } else {
            $inverse = $vv;
        }

        return $inverse;
    }

    /**
     * This function return Greatest Common Divisor for $e and $m numbers
     * @param  integer $e
     * @param  integer $m
     * @return integer
     */
    private function GCD($e, $m)
    {
        $y = $e;
        $x = $m;

        while (bccomp($y, 0) != 0) {
            // modulus function
            $w = bcsub($x, bcmul($y, bcdiv($x, $y, 0)));
            $x = $y;
            $y = $w;
        }

        return $x;
    }

    /**
     * Calculating E under conditions:
     * GCD(N,E) = 1 and 1<E<N
     *
     * @param  integer $m
     * @return integer
     */
    private function findE($m)
    {
        $e = '3';
        if (bccomp($this->GCD($e, $m), '1') != 0) {
            $e = '5';
            $step = '2';
            while (bccomp($this->GCD($e, $m), '1') != 0) {
                $e = bcadd($e, $step);
                if ($step == '2') {
                    $step = '4';
                } else {
                    $step = '2';
                }
            }
        }

        return $e;
    }

    /**
     * ENCRYPT function returns
     * X = M^E (mod N)
     *
     * @param  string  $m
     * @param  integer $e
     * @param  integer $n
     * @param  integer $s
     * @return string
     */
    public function encrypt($m, $e, $n, $s = 3)
    {
        $coded = '';
        $max = strlen($m);
        $packets = ceil($max / $s);
        for ($i = 0; $i < $packets; $i++) {
            $packet = substr($m, $i * $s, $s);
            $code = '0';
            for ($j = 0; $j < $s; $j++) {
                $code = bcadd($code, bcmul(ord($packet[$j]), bcpow('256', $j)));
            }
            $code = bcpowmod($code, $e, $n);
            $coded .= $code . ' ';
        }

        return trim($coded);
    }

    /**
     * DECRYPT function returns
     * M = X^D (mod N)
     * @param  string  $c
     * @param  integer $d
     * @param  integer $n
     * @return string
     */
    public function decrypt($c, $d, $n)
    {
        $coded = split(' ', $c);
        $message = '';
        $max = count($coded);
        for ($i = 0; $i < $max; $i++) {
            $code = bcpowmod($coded[$i], $d, $n);
            while (bccomp($code, '0') != 0) {
                $ascii = bcmod($code, '256');
                $code = bcdiv($code, '256', 0);
                $message .= chr($ascii);
            }
        }

        return $message;
    }

    // Digital Signature
    public function sign($message, $d, $n)
    {
        $messageDigest = md5($message);
        $signature = $this->encrypt($messageDigest, $d, $n, 3);

        return $signature;
    }

    public function prove($message, $signature, $e, $n)
    {
        $messageDigest = $this->decrypt($signature, $e, $n);
        if ($messageDigest == md5($message)) {
            return true;
        } else {
            return false;
        }
    }

    public function signFile($file, $d, $n)
    {
        $messageDigest = md5_file($file);
        $signature = $this->encrypt($messageDigest, $d, $n, 3);

        return $signature;
    }

    public function proveFile($file, $signature, $e, $n)
    {
        $messageDigest = $this->decrypt($signature, $e, $n);
        if ($messageDigest == md5_file($file)) {
            return true;
        } else {
            return false;
        }
    }

}
