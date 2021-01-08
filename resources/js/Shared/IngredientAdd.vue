<template>
  <div class="container">
    <label for="foodgroups">Food Group:</label>
    <select name="foodgroups" id="foodgroups" v-model="foodgroupFilter" @change="updateFoodList">
        <option value="">All</option>
        <option v-for="foodgroup in foodgroups.data" :key="foodgroup.id" :value="foodgroup.id">
            {{ foodgroup.description }}
        </option>
    </select>
    <br/>
    <label for="descriptionSearch">Description Search:</label>
    <input type="text" name="descriptionSearch" id="descriptionSearch" @input="updateFoodList" v-model="descriptionSearchText"/>
    <br/>
    <label for="aliasSearch">Alias Search:</label>
    <input type="text" name="aliasSearch" id="aliasSearch" @input="updateFoodList" v-model="aliasSearchText"/>
    <div class="flex">
        <p>Favourites:</p>
        <div class="ml-2">
            <label for="favouriteYes">Yes</label>
            <input type="radio" name="favourites" id="favouriteYes" value="yes" v-model="favouritesFilter" @change="updateFoodList">
            <label for="favouriteNo">No</label>
            <input type="radio" name="favourites" id="favouriteNo" value="no" checked v-model="favouritesFilter" @change="updateFoodList">
        </div>
    </div>
    <food-list @pageUpdated="updateFoodList" @selectedFood="addFoodAsIngredient" :foods="foods"></food-list>
  </div>
</template>

<script>
import FoodList from "@/Shared/FoodList";

export default {
    components:{
        FoodList
    },
     props:{
        foodgroups: Object,
        foods: Object,
        food: Object
    },
    data(){
        return {
            foodgroupFilter: '',
            aliasSearchText: '',
            descriptionSearchText: '',
            favouritesFilter: ''
        }
    },
    methods:{
        addFoodAsIngredient(newIngredientFoodId) {
            this.$inertia.post(
                this.$route("foods.ingredients.store", {
                    'food': this.food.id
                }), {
                    'ingredient_id':newIngredientFoodId,
                },
                { preserveScroll: false, preserveState: false }
            );
        },

        updateFoodList (page){
            console.log("food", this.food);
            let url = `${this.$route("foods.show", this.food.id)}`;
            url += `?descriptionSearch=${this.descriptionSearchText}`;
            url += `&aliasSearch=${this.aliasSearchText}`;
            url += `&foodgroupSearch=${this.foodgroupFilter}`;
            url += `&favouritesFilter=${this.favouritesFilter}`;

            this.$inertia.visit(url, {
                data:{
                    'page':page
                },
                preserveState: true,
                preserveScroll: true,
            });
        },

    }
}
</script>
