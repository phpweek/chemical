<?php
class BC{
    private $result = '0';
    public function __construct($data=0){
        $this->result = $data;
    }
    public function add($data,$scale=0){
        $this->result = bcplus($this->result,$data,$scale);
        return $this;
    }

    public function adds($scale=0,...$datas){
        foreach($datas as $data){
            $this->result = bcplus($this->result,$data,$scale);
        }
        return $this;
    }

    public function minus($data,$scale=0){
        $this->result = bcminus($this->result,$data,$scale);
        return $this;
    }

    public function mul($data,$scale=0){
        $this->result = bcmul($this->result,$data,$scale);
        return $this;
    }

    public function div($data,$scale=0){
        $this->result = bcdivide($this->result,$data,$scale);
        return $this;
    }

    public function __toString()
    {
        return $this->result;
    }
}

