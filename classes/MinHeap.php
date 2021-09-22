<?php

class MinHeap extends SplMinHeap {
	/*
	Custom comparison method to compare schedule scores
	*/
	public function compare($item1, $item2){
		if(is_numeric($item1) && is_numeric($item2)){
			if($item1 == $item2){
				return 0;
			}

			return $item1 > $item2 ? -1 : 1;
		}
		if($item1->score == $item2->score){
			return 0;
		}

		return $item1->score > $item2->score ? -1 : 1;
	}
}
