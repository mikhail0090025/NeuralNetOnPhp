<?php
enum RoundMethod: int{
    case DontRound = 0;
    case ZeroAndOne = 1;
    case Tanh = 2;
}
interface ICloneable{
    function Clone(): ICloneable;
}
class NodeNN{
    protected float $value;
    protected bool $rounded;
    public function __construct(){
        $this->rounded = false;
        $this->value = 0;
    }
    public function SetValue($val){
        $this->value = $val;
        $this->rounded = false;
    }
    public function GetValue(): float{
        return $this->value;
    }
    public function Reset(){
        $this->rounded = false;
        $this->value = 0;
    }
    public function Round(RoundMethod $roundMethod){
        if($this->rounded) return;
        switch ($roundMethod) {
            case 0:
                break;
            case 1:
                $this->value = $this->value > 0 ? 1:0;
                break;
            case 2:
                $this->value = tanh($this->value);
                break;
            default:
                break;
        }
        $this->rounded = true;
    }
}
class Layer{
    public Layer $NextLayer;
    public Array $Nodes;
    protected readonly int $size;
    protected Array $sinnapses;
    public function Size(): int{ return $this->size; }
    public function Reset(){
        foreach($this->Nodes as $node){
            $node->Reset();
        }
    }
    public function Round(RoundMethod $roundMethod){
        foreach($this->Nodes as $node){
            $node->Round($roundMethod);
        }
    }
    public function CalcNextLayer(RoundMethod $roundMethod){
        $this->NextLayer->Reset();
        for ($i=0; $i < $this->size; $i++) { 
            for ($j=0; $j < $this->NextLayer->Nodes; $j++) { 
                $this->NextLayer->Nodes[$j]->SetValue($this->NextLayer->Nodes[$j]->GetValue() * $this->sinnapses[$i][$j]);
            }
        }
    }
    public function __construct(int $size_, ?Layer $next_layer = null){
        $this->Nodes = [];
        $this->sinnapses = [];
        for ($i=0; $i < $size_; $i++) { 
            array_push($this->Nodes, new NodeNN());
        }
        if(isset($next_layer)){
            $this->NextLayer = $next_layer;
            for ($i=0; $i < $size_; $i++) {
                array_push($this->sinnapses, Array());
                for ($j=0; $j < $this->NextLayer->Size(); $j++) {
                    array_push($this->sinnapses[$i], rand(-10000, 10000) / (float)10000); 
                }
            }
        }
    }
}
?>