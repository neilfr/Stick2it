<template>
  <div>
    <div class="flex justify-between mt-8">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Ingredients
        </h2>
        <button @click="showAddIngredientModal">
            <img class="w-6" src="/images/add-outline.svg">
        </button>
    </div>

    <modal class="z-30" :showing="this.showAddIngredientModalProp" @close="closeAddIngredientModal">
        <template v-slot:title>
            Add Ingredient
        </template>
        <ingredient-add :foodgroups="foodgroups" :foods="foods" :food="food"></ingredient-add>
    </modal>

      <table class="z-20 table-fixed w-full mt-8">
        <tr>
            <th>Alias</th>
            <th class="w-1/3">Description</th>
            <th class="text-left translate-x-4 transform -rotate-45 origin-bottom-left">KCal</th>
            <th class="text-left translate-x-4 transform -rotate-45 origin-bottom-left">Protein</th>
            <th class="text-left translate-x-4 transform -rotate-45 origin-bottom-left">Fat</th>
            <th class="text-left translate-x-4 transform -rotate-45 origin-bottom-left">Carbohydrate</th>
            <th class="text-left translate-x-4 transform -rotate-45 origin-bottom-left">Potassium</th>
            <th class="text-left translate-x-4 transform -rotate-45 origin-bottom-left">Quantity</th>
            <th class="text-left translate-x-4 transform -rotate-45 origin-bottom-left">Actions</th>
        </tr>
        <tr class="odd:bg-gray-100 leading-9" v-for="ingredient in food.ingredients" :key="ingredient.food_ingredient_id">
            <td>{{ingredient.alias}}</td>
            <td class="truncate">{{ingredient.description}}</td>
            <td>{{Math.round(ingredient.kcal)}}</td>
            <td>{{Math.round(ingredient.protein)}}</td>
            <td>{{Math.round(ingredient.fat)}}</td>
            <td>{{Math.round(ingredient.carbohydrate)}}</td>
            <td>{{Math.round(ingredient.potassium)}}</td>
            <td>{{ingredient.quantity}}</td>
            <td class="text-center flex justify-between">
                <button @click="showIngredientQuantityModal(ingredient)">
                    <img class="w-6" src="/images/edit-pencil.svg">
                </button>
                <button @click="removeIngredient(ingredient)">
                    <img class="w-6" src="/images/trash.svg">
                </button>
            </td>
        </tr>
        <modal :showing="this.showIngredientQuantityModalProp" @close="closeIngredientQuantityModal">
            <template v-slot:title>
                Change ingredient quantity
            </template>
            <update-quantity
                :id="selectedIngredient.id"
                :initialValue="selectedIngredient.quantity"
                @update="update"
            />
        </modal>
      </table>
    <ingredient-add v-if="showIngredientAdd" :foodgroups="foodgroups" :foods="foods" :food="food"></ingredient-add>
  </div>
</template>

<script>

import UpdateNumberModal from "@/Shared/UpdateNumberModal";
import IngredientAdd from "@/Shared/IngredientAdd";
import Modal from "@/Shared/Modal";
import UpdateQuantity from '@/Shared/UpdateQuantity.vue';

export default {
    components:{
        UpdateNumberModal,
        IngredientAdd,
        Modal,
        UpdateQuantity
    },
    props:{
        food:Object,
        foodgroups:Object,
        foods:Object
    },
    data(){
        return{
            showAddIngredientModalProp: false,
            showIngredientQuantityModalProp: false,
            showIngredientAdd: false,
            show: false,
            selectedIngredient: 1,
            url: null,
            params: null
        }
    },
    methods:{
        showAddIngredientModal(){
            this.showAddIngredientModalProp = true;
        },
        closeAddIngredientModal(){
            this.showAddIngredientModalProp = false;
        },
        showIngredientQuantityModal(ingredient){
            this.selectedIngredient = ingredient;
            this.showIngredientQuantityModalProp = true;
        },
        closeIngredientQuantityModal(){
            this.showIngredientQuantityModalProp = false;
        },
        close(){
            this.show = false;
        },
        open(ingredient){
            this.selectedIngredient = ingredient;
            this.show = true;
        },
        update(value){
            this.$inertia.patch(this.$route("foods.ingredients.update", {
                food : this.food.id,
                ingredient: this.selectedIngredient.id
            }), {
                quantity: value
            },{
                preserveScroll:true,
                preserveState:false
            }).then((res)=>{
                this.close();
            });
        },
        removeIngredient(ingredient){
            this.$inertia.delete(this.$route("foods.ingredients.destroy", {
                    'food': this.food.id,
                    'ingredient': ingredient.id
                }));
        },
        showIngredients () {
            console.log("testing");
            this.showIngredientAdd=!this.showIngredientAdd;
        },
    }
}
</script>
