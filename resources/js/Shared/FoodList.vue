<template>
  <div>
    <table>
        <tr>
            <th>Fav</th>
            <th>Alias</th>
            <th class="w-1/3">Description</th>
            <th class="text-left translate-x-4 transform -rotate-45 origin-bottom-left">KCal</th>
            <th class="text-left translate-x-4 transform -rotate-45 origin-bottom-left">Protein</th>
            <th class="text-left translate-x-4 transform -rotate-45 origin-bottom-left">Fat</th>
            <th class="text-left translate-x-4 transform -rotate-45 origin-bottom-left">Carbohydrate</th>
            <th class="text-left translate-x-4 transform -rotate-45 origin-bottom-left">Potassium</th>
            <th class="text-left translate-x-4 transform -rotate-45 origin-bottom-left">Base Quantity</th>
            <th class="text-left translate-x-4 transform -rotate-45 origin-bottom-left">Actions</th>
        </tr>
        <tr v-for="food in foods.data" :key="food.id" class="odd:bg-gray-100">
            <td>
                <input type="checkbox" :id="food.id" :value="food.favourite" :checked="food.favourite" disabled/>
            </td>
            <td>{{food.alias}}</td>
            <td class="truncate">{{food.description}}</td>
            <td>{{food.kcal}}</td>
            <td>{{food.protein}}</td>
            <td>{{food.fat}}</td>
            <td>{{food.carbohydrate}}</td>
            <td>{{food.potassium}}</td>
            <td>{{food.base_quantity}}</td>
            <td>
                <button
                    @click="selectFood"
                    :id="food.id"
                    :selectedFoodBaseQuantity="food.quantity"
                >
                    Add
                </button>
            </td>
        </tr>
      </table>
        <div>
            <button @click="goToPageOne">First</button>
            <button @click="previousPage">Previous</button>
            <button @click="nextPage">Next</button>
            <button @click="lastPage">Last</button>
        </div>
        <div>
            <p>Page: {{foods.meta.current_page}} of {{foods.meta.last_page}}</p>
        </div>
  </div>
</template>

<script>

export default {
    props:{
        foods: Object,
    },
    data(){
        return{
            page: 1,
            selectedFoodId: null,
            selectedFoodBaseQuantity: null
        }
    },
    methods:{
        goToPageOne(){
            this.page=1;
            this.goToPage(1);
        },
        previousPage(){
            if(this.page>1){
                this.page--;
                this.goToPage();
            }
        },
        nextPage(){
            if(this.page<this.foods.meta.last_page){
                this.page++;
                this.goToPage();
            }
        },
        lastPage(){
            this.page = this.foods.meta.last_page;
            this.goToPage();
        },
        goToPage(){
            this.$emit('pageUpdated', this.page);
        },
        selectFood(e){
            this.$emit('selectedFood', e.target.id);
        }
    }
}
</script>
