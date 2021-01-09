<template>
  <div>
    <h2>Ingredients</h2>
    <button @click="showAddIngredientModal">Add Ingredient</button>
    <modal :showing="this.showAddIngredientModalProp" @close="closeAddIngredientModal">
        <template v-slot:title>
            Add Ingredient
        </template>
        <ingredient-add :foodgroups="foodgroups" :foods="foods" :food="food"></ingredient-add>
    </modal>

      <table>
        <tr>
            <th>Alias</th>
            <th>Description</th>
            <th>KCal</th>
            <th>Protein</th>
            <th>Fat</th>
            <th>Carbohydrate</th>
            <th>Potassium</th>
            <th>Quantity</th>
            <th>Actions</th>
        </tr>
        <tr v-for="ingredient in food.ingredients" :key="ingredient.food_ingredient_id">
            <td>{{ingredient.alias}}</td>
            <td>{{ingredient.description}}</td>
            <td>{{Math.round(ingredient.kcal)}}</td>
            <td>{{Math.round(ingredient.protein)}}</td>
            <td>{{Math.round(ingredient.fat)}}</td>
            <td>{{Math.round(ingredient.carbohydrate)}}</td>
            <td>{{Math.round(ingredient.potassium)}}</td>
            <td>{{ingredient.quantity}}</td>
            <td><button @click="showIngredientQuantityModal(ingredient)">Edit</button><button @click="removeIngredient(ingredient)">Delete</button></td>
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
