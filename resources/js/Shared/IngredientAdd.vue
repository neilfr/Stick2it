<template>
    <div class="container">
        <section class="py-2 grid grid-cols-12 gap-2 mb-8">
            <label class="col-span-2" for="aliasSearch">Alias:</label>
            <input class="border col-span-4" type="text" name="aliasSearch" id="aliasSearch" @input="updateFoodList" v-model="aliasSearchText" placeholder="Alias Search"/>
            <div class="col-span-6"></div>

            <label class="col-span-2" for="descriptionSearch" placeholder="Description Search">Description:</label>
            <input class="border col-span-4" type="text" name="descriptionSearch" id="descriptionSearch" @input="updateFoodList" v-model="descriptionSearchText" placeholder="Description Search"/>
            <div class="col-span-6"></div>

            <label class="col-span-2" for="foodgroups">Food Group:</label>

            <select class="border col-span-4" name="foodgroups" id="foodgroups" v-model="foodgroupFilter" @change="updateFoodList">
                <option value="">All</option>
                <option v-for="foodgroup in foodgroups.data" :key="foodgroup.id" :value="foodgroup.id">
                    {{ foodgroup.description }}
                </option>
            </select>
            <div class="col-span-6"></div>

            <div class="flex col-span-2">
                <p>Favourites:</p>
            </div>
            <div>
                <label for="favouriteYes">Yes</label>
                <input type="radio" name="favourites" id="favouriteYes" value="yes" v-model="favouritesFilter" @change="updateFoodList">
            </div>
            <div>
                <label for="favouriteNo">No</label>
                <input type="radio" name="favourites" id="favouriteNo" value="no" checked v-model="favouritesFilter" @change="updateFoodList">
            </div>
            <div class="col-span-6"></div>
        </section>
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
