<?php
namespace App\Services;

use Osms\Osms;

class TransactionCode{
    public function generatedCode(): string{
        return $this->random().'-'.$this->random().'-'.$this->random();
    }

    public function random(){
        $random_val = '0123456789';
        return substr(str_shuffle(str_repeat($x=$random_val, ceil(3/strlen($x)) )),1,3);
    }


    public function frais($montant): array{
        $frais=0;
        $montant = abs($montant);
        if($montant>0 && $montant <=5000){
            $frais = 425;
        }
        elseif($montant>5000 && $montant <=10000){
            $frais = 850;
        }
        elseif($montant>10000 && $montant <=15000){
            $frais = 1270;
        }elseif($montant>15000 && $montant <=20000){
            $frais = 1695;
        }elseif($montant>20000 && $montant <=50000){
            $frais = 2500;
        }elseif($montant>50000 && $montant <=60000){
            $frais = 3000;
        }elseif($montant>60000 && $montant <=75000){
            $frais = 4000;
        }elseif($montant>75000 && $montant <=120000){
            $frais = 5000;
        }elseif($montant>120000 && $montant <=150000){
            $frais = 6000;
        }elseif($montant>150000 && $montant <=200000){
            $frais = 7000;
        }elseif($montant>200000 && $montant <=250000){
            $frais = 8000;
        }elseif($montant>250000 && $montant <=300000){
            $frais = 9000;
        }elseif($montant>300000 && $montant <=400000){
            $frais = 12000;
        }elseif($montant>400000 && $montant <=750000){
            $frais = 15000;
        }elseif($montant>750000 && $montant <=900000){
            $frais = 22000;
        }elseif($montant>900000 && $montant <=1000000){
            $frais = 25000;
        }elseif($montant>1000000 && $montant <=1125000){
            $frais = 27000;
        }elseif($montant>1125000 && $montant <=2000000){
            $frais = 30000;
        }
        elseif($montant>2000000){
            $frais = floor(($montant*2/100));
        }

        $partEtat = ($frais/100)*40;
        $partSystem = ($frais/100)*30;
        $partAgence = ($frais/100)*30;
        
        return  array('frais' => $frais, 'etat' => $partEtat, 'systeme' =>$partSystem, 'agence' =>$partAgence );
    }

    public function envoiArgentSMS($receiverNumber, $sender, $montant, $code){
        $config = array(
            'clientId' => 'dT2dAajN6AjG0Doa3Tbz7YkXzXYDnAyw',
            'clientSecret' => 'R9d6kAXZ8qeWa7jn'
        );
        
        $osms = new Osms($config);
        
        // retrieve an access token
        $response = $osms->getTokenFromConsumerKey();
        
        if (!empty($response['access_token'])) {
            // dd($response['access_token']);
            $senderAddress = 'tel:+2210000';
            $receiverAddress = 'tel:+221'.$receiverNumber;
            $message = 'Bonjour. '.$sender.' vous a envoyés '.$montant.'f. via SA transfert d\'argent. Code de retrait: '.$code;
            $senderName = 'SA transfert d\'argent';
        
            $osms->sendSMS($senderAddress, $receiverAddress, $message, $senderName);
        } else {
            // error
            dd('error');
        }
    }

    public function retraitArgentSMS($receiverNumber, $beneficiaire, $montant){
        $config = array(
            'clientId' => 'your_client_id',
            'clientSecret' => 'your_client_secret'
        );
        
        $osms = new Osms($config);
        
        // retrieve an access token
        $response = $osms->getTokenFromConsumerKey();
        
        if (!empty($response['access_token'])) {
            $senderAddress = 'tel:+2210000';
            $receiverAddress = 'tel:+221'.$receiverNumber;
            $message = 'Bonjour. Les '.$montant.'f envoyés à '.$beneficiaire.' viennent d\'être retirés. Merci et à bientôt';
            $senderName = 'SA transfert d\'argent';
        
            $osms->sendSMS($senderAddress, $receiverAddress, $message, $senderName);
        } else {
            // error
        }
    }
}