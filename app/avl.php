<?php

    class AVL {

        protected $nodes = [];
        protected $insert_id = 0;

        public function loadNodes($nodes) {
            $this->nodes = $nodes;
            $this->insert_id = max(array_keys($nodes))+1;
        }

        protected function setNode($value,$left,$right,$height,$id) {
            $this->nodes[$id] = [
                "id" => $id,
                "value" => $value,
                "left" => $left,
                "right" => $right,
                "height" => $height
            ];
        }

        protected function getNodeHeight($node) {
            if (isset($this->nodes[$node])) {
                return $this->nodes[$node]["height"];
            }
            return 0;
        }

        protected function getNodeValue($node) {
            if (isset($this->nodes[$node])) {
                return $this->nodes[$node]["value"];
            }
            return null;
        }

        protected function getLeftNode($node) {
            if (isset($this->nodes[$node])) {
                return $this->nodes[$node]["left"];
            }
            return null;
        }

        protected function getRightNode($node) {
            if (isset($this->nodes[$node])) {
                return $this->nodes[$node]["right"];
            }
            return null;
        }

        public function insert ($value, $node = 0) {
            if (!isset($this->nodes[$node])) {
                $this->setNode($value,null,null,1,$this->insert_id);
                return $this->insert_id++;
            }

            if ($value < $this->getNodeValue($node)) {
                $left_node = $this->getLeftNode($node);
                $new_node_id = $this->insert($value, $left_node);
                if (is_null($left_node)) {
                    $this->nodes[$node]["left"] = $new_node_id;
                }
                $this->reBalance($node);
                return $node;
            }

            $right_node = $this->getRightNode($node);
            $new_node_id = $this->insert($value, $right_node);
            if (is_null($right_node)) {
                $this->nodes[$node]["right"] = $new_node_id;
            }
            $this->reBalance($node);
            return $node;
        }

        protected function isBalanced($node) {
            return abs($this->getNodeHeight($this->getLeftNode($node)) - $this->getNodeHeight($this->getRightNode($node))) < 2;
        }

        protected function leftRotate($node) {
            $nodeA = $this->nodes[$node];
            $nodeB = $this->nodes[$nodeA["right"]];

            if ($this->getNodeHeight($nodeB["left"]) < $this->getNodeHeight($nodeB["right"])) {
                //Small rotate
                $nodeL = $nodeA["left"];
                $nodeC = $nodeB["left"];
                $nodeR = $nodeB["right"];
                $newAId = $nodeB["id"];
                $newBId = $nodeA["id"];
                $this->setNode($nodeA["value"],$nodeL,$nodeC,max($this->getNodeHeight($nodeL),$this->getNodeHeight($nodeC))+1,$newAId); //Set new A
                $this->setNode($nodeB["value"],$nodeB["id"],$nodeR,max($this->getNodeHeight($nodeB["id"]),$this->getNodeHeight($nodeR))+1,$newBId); //Set new B
            } else {
                //Big rotate
                $nodeC = $this->nodes[$nodeB["left"]];
                $nodeL = $nodeA["left"];
                $nodeM = $nodeC["left"];
                $nodeN = $nodeC["right"];
                $nodeR = $nodeB["right"];
                $newAId = $nodeC["id"];
                $newCId = $nodeA["id"];
                $this->setNode($nodeA["value"],$nodeL,$nodeM,max($this->getNodeHeight($nodeL),$this->getNodeHeight($nodeM))+1,$newAId); //Set new A
                $this->setNode($nodeB["value"],$nodeN,$nodeR,max($this->getNodeHeight($nodeN),$this->getNodeHeight($nodeR))+1,$nodeB["id"]); //Set new B
                $this->setNode($nodeC["value"],$newAId,$nodeB["id"],max($this->getNodeHeight($newAId),$this->getNodeHeight($nodeB["id"]))+1,$newCId); //Set new C
            }
        }

        protected function rightRotate($node) {
            $nodeA = $this->nodes[$node];
            $nodeB = $this->nodes[$nodeA["left"]];

            if ($this->getNodeHeight($nodeB["left"]) > $this->getNodeHeight($nodeB["right"])) {
                //Small rotate
                $nodeL = $nodeB["left"];
                $nodeC = $nodeB["right"];
                $nodeR = $nodeA["right"];
                $newAId = $nodeB["id"];
                $newBId = $nodeA["id"];
                $this->setNode($nodeA["value"],$nodeC,$nodeR,max($this->getNodeHeight($nodeC),$this->getNodeHeight($nodeR))+1,$newAId); //Set new A
                $this->setNode($nodeB["value"],$nodeL,$nodeB["id"],max($this->getNodeHeight($nodeL),$this->getNodeHeight($nodeB["id"]))+1,$newBId); //Set new B
            } else {
                //Big rotate
                $nodeC = $this->nodes[$nodeB["right"]];
                $nodeL = $nodeB["left"];
                $nodeM = $nodeC["left"];
                $nodeN = $nodeC["right"];
                $nodeR = $nodeA["right"];
                $newAId = $nodeC["id"];
                $newCId = $nodeA["id"];
                $this->setNode($nodeA["value"],$nodeN,$nodeR,max($this->getNodeHeight($nodeN),$this->getNodeHeight($nodeR))+1,$newAId); //Set new A
                $this->setNode($nodeB["value"],$nodeL,$nodeM,max($this->getNodeHeight($nodeL),$this->getNodeHeight($nodeM))+1,$nodeB["id"]); //Set new B
                $this->setNode($nodeC["value"],$nodeB["id"],$newAId,max($this->getNodeHeight($nodeB["id"]),$this->getNodeHeight($newAId))+1,$newCId); //Set new C
            }
        }

        protected function reBalance ($node) {
            if ($this->getLeftNode($node) && !isset($this->nodes[$this->getLeftNode($node)])) {
                $this->nodes[$node]["left"] = null;
            }
            if ($this->getRightNode($node) && !isset($this->nodes[$this->getRightNode($node)])) {
                $this->nodes[$node]["right"] = null;
            }


            $this->nodes[$node]["height"] = max($this->getNodeHeight($this->getLeftNode($node)),$this->getNodeHeight($this->getRightNode($node)))+1;
            if ($this->isBalanced($node)) {
                return;
            }

            if ($this->getNodeHeight($this->getLeftNode($node)) < $this->getNodeHeight($this->getRightNode($node))) {
                $this->leftRotate($node);
            } else {
                $this->rightRotate($node);
            }

        }

        public function delete($value,$node = 0) {
            if (is_null($node)) {
                return;
            }
            if ($value == $this->getNodeValue($node)) {
                if (!is_null($this->getLeftNode($node))) {
                    $this->findMaxLeft($this->getLeftNode($node),$node);
                } elseif (!is_null($this->getRightNode($node))) {
                    $this->findMinRight($this->getRightNode($node),$node);
                } else {
                    unset($this->nodes[$node]);
                    return;
                }
            } elseif ($value < $this->getNodeValue($node)) {
                $this->delete($value,$this->getLeftNode($node));
            } else {
                $this->delete($value, $this->getRightNode($node));
            }
            $this->reBalance($node);
            return;
        }

        public function findMaxLeft($node,$targetNode) {
            if ($this->getRightNode($node)) {
                $this->findMaxLeft($this->getRightNode($node),$targetNode);
            } else {
                $this->nodes[$targetNode]["value"] = $this->getNodeValue($node);
                if ($this->getLeftNode($node)) {
                    $this->setNode($this->getNodeValue($this->getLeftNode($node)),$this->getLeftNode($this->getLeftNode($node)),$this->getRightNode($this->getLeftNode($node)),$this->getNodeHeight($this->getLeftNode($node)),$node);
                } else {
                    unset($this->nodes[$node]);
                    return;
                }
            }
            $this->reBalance($node);
            return;
        }

        public function findMinRight($node,$targetNode) {
            if ($this->getLeftNode($node)) {
                $this->findMinRight($this->getLeftNode($node),$targetNode);
            } else {
                $this->nodes[$targetNode]["value"] = $this->getNodeValue($node);
                if ($this->getRightNode($node)) {
                    $this->setNode($this->getNodeValue($this->getRightNode($node)),$this->getLeftNode($this->getRightNode($node)),$this->getRightNode($this->getRightNode($node)),$this->getNodeHeight($this->getRightNode($node)),$node);
                } else {
                    unset($this->nodes[$node]);
                    return;
                }
            }
            $this->reBalance($node);
            return;
        }

        public function find($value,$node = 0) {
            if (is_null($node)) {
                return null;
            }
            if ($value == $this->getNodeValue($node)) {
                return $node;
            }
            if ($value < $this->getNodeValue($node)) {
                return $this->find($value,$this->getLeftNode($node));
            }
            return $this->find($value,$this->getRightNode($node));
        }

        public function toArray($node = 0) {
            if (isset($this->nodes[$node])) {
                $left_node = $this->toArray($this->getLeftNode($node));
                $right_node = $this->toArray($this->getRightNode($node));
                $result = [
                    $this->getNodeValue($node) . " [" . $this->getNodeHeight($node) . "]" => []
                ];
                if (!is_null($left_node)) {
                    $result[$this->getNodeValue($node) . " [" . $this->getNodeHeight($node) . "]"][array_key_first($left_node)] = array_pop($left_node);
                } else {
                    $result[$this->getNodeValue($node) . " [" . $this->getNodeHeight($node) . "]"]["LEFT:NULL"] = "NULL";
                }

                if (!is_null($right_node)) {
                    $result[$this->getNodeValue($node) . " [" . $this->getNodeHeight($node) . "]"][array_key_first($right_node)] = array_pop($right_node);
                } else {
                    $result[$this->getNodeValue($node) . " [" . $this->getNodeHeight($node) . "]"]["RIGHT:NULL"] = "NULL";
                }


                return $result;
            }

            return null;
        }

        public function duplicate() {
            $newAVL = new AVL();
            $newAVL->loadNodes($this->nodes);
            return $newAVL;
        }

    }