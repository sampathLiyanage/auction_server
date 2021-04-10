<?php

namespace Tests\Feature;

use App\Item;
use App\User;
use Tests\TestCase;

class ItemControllerTest extends TestCase
{
    public function testShow() {
        $items = $this->populateItems(5,1);
        $response1 = $this->json('GET', 'api/items/'.$items[3]->id)
            ->assertStatus(200);
        $responseData1 = json_decode($response1->getContent(),true);
        $this->assertEquals($items[3]->id, $responseData1['id']);
        $this->assertEquals($items[3]->name, $responseData1['name']);
        $this->assertEquals($items[3]->description, $responseData1['description']);
        $this->assertEquals($items[3]->price, $responseData1['price']);
        $this->assertEquals($items[3]->auction_end_time, $responseData1['auction_end_time']);
        $this->assertEquals($items[3]->owner_id, $responseData1['owner_id']);
        $response2 = $this->json('GET', 'api/items/'.$items[5]->id)
            ->assertStatus(200);
        $responseData2 = json_decode($response2->getContent(),true);
        $this->assertEquals($items[5]->id, $responseData2['id']);
        $this->assertEquals($items[5]->name, $responseData2['name']);
        $this->assertEquals($items[5]->description, $responseData2['description']);
        $this->assertEquals($items[5]->price, $responseData2['price']);
        $this->assertEquals($items[5]->auction_end_time, $responseData2['auction_end_time']);
        $this->assertEquals($items[5]->owner_id, $responseData2['owner_id']);
    }

    /**
     * @dataProvider indexValidationDataProvider
     * @param $filter
     * @param $offset
     * @param $limit
     * @param $sortField
     * @param $sortOrder
     * @param $statusCode
     */
    public function testIndexValidation($filter, $offset, $limit, $sortField, $sortOrder, $statusCode) {
        $this->json('GET', 'api/items',
            ['filter'=>$filter, 'offset'=>$offset, 'limit'=>$limit, 'sortField'=>$sortField, 'sortOrder'=>$sortOrder])
            ->assertStatus($statusCode);
    }

    public function indexValidationDataProvider() {
        return [
            ['asdfd', 10, 3, 'name', 'asc', 200],
            ['asdfd', '10', '3', 'name', 'ASC', 200],
            ['asdfd', '10', '3', 'name', 'desc', 200],
            ['asdfd', '10', '3', 'name', 'DESC', 200],
            ['asdfd', '10', '3', 'name', 'Desc', 400],
            [343, '10', '3', 'name', 'desc', 400],
            ['asdfd', -10, '3', 'name', 'desc', 400],
            ['asdfd', 10, '-3', 'name', 'desc', 400],
            ['asdfd', '10', '3', 'price', 'desc', 200],
            ['asdfd', '10', '3', 'auction_end_time', 'desc', 200]
        ];
    }

    public function testIndexFilter() {
        $search = 'en';
        $items = $this->populateItems(50,6);
        $response = $this->json('GET', 'api/items',
            ['filter'=>$search])
            ->assertStatus(200);
        $responseData = json_decode($response->getContent(),true);
        $itemsMatchingNameOrDescription = [];
        foreach ($items as $item) {
            if (strpos($search, strtolower($item->name)) !== false
                || strpos($search, strtolower($item->description)) !== false) {
                $itemsMatchingNameOrDescription[] = $item->id;
            }
        }
        $resultIds = array_map(function($result) {
            return $result['id'];
        }, $responseData);
        $this->assertTrue(empty(array_diff($itemsMatchingNameOrDescription, $resultIds)));
    }

    /**
     * @dataProvider indexPaginationDataProvider
     * @param $offset
     * @param $limit
     * @param $count
     * @param $firstId
     * @param $lastId
     */
    public function testIndexPagination($offset, $limit, $count, $firstId, $lastId) {
        $this->populateItems(55,0);
        $response = $this->json('GET', 'api/items',
            ['offset'=>$offset, 'limit'=>$limit])
            ->assertStatus(200);
        $responseData = json_decode($response->getContent(),true);
        $this->assertCount($count, $responseData);
        if ($count > 0) {
            $this->assertEquals($responseData[0]['id'], $firstId);
            $this->assertEquals($responseData[count($responseData)-1]['id'], $lastId);
        }
    }

    public function indexPaginationDataProvider() {
        return [
            [0, 0, 0, 0, 0],
            [3, 0, 0, 0, 0],
            [0, 1, 1, 1, 1],
            [0, 10, 10, 1, 10],
            [3, 10, 10, 4, 13],
            [3, 100, 52, 4, 55],
        ];
    }

    /**
     * @dataProvider indexSortingDataProvider
     * @param $sortField
     * @param $sortOrder
     * @param $idOrder
     */
    public function testIndexSorting($sortField, $sortOrder, $idOrder) {
        factory(Item::class)->create(
            ['name'=>'name1', 'price'=>'2353.21', 'auction_end_time'=>'2021-01-02 13:12']
        );
        factory(Item::class)->create(
            ['name'=>'name3', 'price'=>'53.21', 'auction_end_time'=>'2021-01-02 12:12']
        );
        factory(Item::class)->create(
            ['name'=>'name2', 'price'=>'134', 'auction_end_time'=>'2021-04-02 13:12']
        );
        factory(Item::class)->create(
            ['name'=>'name4', 'price'=>'512', 'auction_end_time'=>'2021-01-02 13:11']
        );
        factory(Item::class)->create(
            ['name'=>'name5', 'price'=>'2353.20', 'auction_end_time'=>'2022-01-02 13:12']
        );
        $response = $this->json('GET', 'api/items',
            ['sortField'=>$sortField, 'sortOrder'=>$sortOrder])
            ->assertStatus(200);
        $responseData = json_decode($response->getContent(),true);
        for($i=0; $i<count($idOrder); $i++) {
            $this->assertEquals($idOrder[$i], $responseData[$i]['id']);
        }
    }

    public function indexSortingDataProvider() {
        return [
            ['name', 'asc', [1,3,2,4,5]],
            ['name', 'desc', [5,4,2,3,1]],
            ['price', 'asc', [2,3,4,5,1]],
            ['price', 'desc', [1,5,4,3,2]],
            ['auction_end_time', 'asc', [2,4,1,3,5]],
            ['auction_end_time', 'desc', [5,3,1,4,2]],
        ];
    }

    protected function populateItems($countWithoutOwners, $countWithOwners) {
        $user = factory(User::class)->create();
        $items = [];
        for ($i=0; $i<$countWithoutOwners; $i++) {
            $items[] = factory(Item::class)->create();
        }
        for ($i=0; $i<$countWithOwners; $i++) {
            $items[] = factory(Item::class)->create(['owner_id'=>$user->id]);
        }
        return $items;
    }
}
