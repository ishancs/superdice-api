<?php if(!defined('BASEPATH')){exit('No direct script access allowed');}
    class Wallet_model extends CI_Model{

            public function getWallets($wallet_address, $network)
        {
            $wallets = [];
            if($wallet_address){
                $wallets = $this->db->select('*')->from("tbl_wallet")->where(array("network"=>$network, "status"=>1))->get()->result();
                foreach($wallets as $wallet){
                    $wallet->value_options = json_decode($wallet->value_options);
                    $wallet->value = $this->db->select('*')->from("tbl_transactions")->where(array("wallet_address"=>$wallet_address, "network"=>$wallet->network, "currency"=>$wallet->currency))->order_by("created_at","desc")->get()->row();
                }
            }
            return $wallets;
        }

            public function getTransactions($wallet_address)
        {
            $transactions = [];
            if($wallet_address){
                $transactions = $this->db->select('*')->from("tbl_transactions")->where(array("wallet_address"=>$wallet_address, "status"=>1))->get()->result();
            }
            return $transactions;       
        }

            public function createBoard()
        {
            $board = $this->db->select('*')->from("tbl_boards")->order_by("id","desc")->get()->row();
            if(@$board->drawn){
                $data['drawn'] = "";
                $data['status'] = 1;
                $this->db->insert('tbl_boards', $data);
                $board_id = $this->db->insert_id();
                $board = $this->db->select('*')->from("tbl_boards")->where("id", $board_id)->get()->row();
            }
            if($board){
                $board->bets = $this->db->select('*')->from("tbl_board_bets")->where("board_id", $board->id)->get()->result();
            }
            return $board;
        }

            public function updateBoard($data, $board_id)
        {
            $update = $this->db->where(array('id'=> $board_id))->update('tbl_boards', $data);
            if($update){
                return true;
            }else{
                return false;
            }
        }

            public function existBoard($board_id="")
        {
            if($board_id){
                $res = $this->db->select('*')->from("tbl_boards")->where(array("id"=> $board_id, "drawn"=> ""))->get()->row();
            }else{
                $res = $this->db->select('*')->from("tbl_boards")->where(array("drawn"=> ""))->order_by("id","desc")->get()->row();
            }
            return $res;
        }

            public function getBoard($board_id="")
        {
            $res = $this->db->select('*')->from("tbl_boards")->where(array("id"=> $board_id))->get()->row();
            return $res;
        }

            public function existBets($board_id)
        {
            $bets = $this->db->select('*')->from("tbl_board_bets")->where("board_id", $board_id)->get()->result();
            return $bets;
        }

            public function getLastWalletHistory($wallet_address, $network, $currency)
        {
            $transaction = $this->db->select('*')->from("tbl_transactions")->where(array("wallet_address"=> $wallet_address, "network"=> $network, "currency"=> $currency))->order_by("created_at", "desc")->get()->row();
            return $transaction;
        }

            public function existTransaction($txn_token)
        {
            if($txn_token){
                $res = $this->db->select('*')->from("tbl_transactions")->where(array("txn_token"=> $txn_token))->get()->row();
                if($res){
                    $result = array(
                        'success' => false,
                        'message' =>"txn_token not valid or exist"
                    );
                    echo json_encode($result);
                    exit;
                }
            }
            return;
        }

            public function updateTransaction($data, $wallet_address, $network, $currency)
        {
            $update = $this->db->where(array('wallet_address'=> $wallet_address, 'network'=> $network, 'currency'=> $currency))->update('tbl_transactions', $data);
            if($update){
                return true;
            }else{
                return false;
            }
        }

            public function submitTransaction($data)
        {
            $res = $this->db->insert('tbl_transactions',$data);
            if($res) {
                return $this->db->insert_id();
            }else{
                return false;
            }
        }

        
            public function existSameBet($data)
        {
            $res = $this->db->select('*')->from("tbl_board_bets")->where(array("board_id"=>$data['board_id'], "wallet_address"=>$data['wallet_address'], "network"=> $data['network'], "currency"=> $data['currency'], "side"=> $data['side']))->get()->row();
            return $res;
        }

            public function submitBet($data)
        {
            $res = $this->db->insert('tbl_board_bets',$data);
            if($res) {
                return $this->db->insert_id();
            }else{
                return false;
            }
        }

            public function updateBet($id, $data)
        {
            $update = $this->db->where(array('id'=> $id))->update('tbl_board_bets', $data);
            if($update){
                return true;
            }else{
                return false;
            }
        }

        

        //     public function getWhitelistRecords($server_type)
        // {
        //     $total = $this->db->select('count(*) as total')->from("tbl_whitelists")->where(array("server_type"=> $server_type))->get()->row()->total;
        //     return $total;
        // }
        

        


        
    }
    ?>