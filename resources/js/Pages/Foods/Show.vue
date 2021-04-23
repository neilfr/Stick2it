<template>
    <app-layout>
        <template #header>
            <div class="flex justify-between">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Food
                </h2>
                <button @click="cancelFoodUpdate">
                    <img class="w-6" src="/images/close.svg">
                </button>
            </div>
        </template>
    <div>
        <div class="grid grid-cols-12 gap-2">
            <p class="col-span-12" v-if="errors.description">{{errors.description}}</p>
            <label class="col-span-2" for="description">Description:</label>
            <input class="border rounded col-span-4" id="description" type="text" :readonly="!food.data.editable" v-model="food.data.description">
            <div class="col-span-6"></div>

            <div class="col-span-12" v-if="errors.alias">{{errors.alias}}</div>
            <label class="col-span-2" for="alias">Alias:</label>
            <input class="border rounded" id="alias" type="text" :readonly="!food.data.editable" v-model="food.data.alias"/>
            <div class="col-span-9"></div>
            <span class="col-span-2" v-if="food.data.editable"></span>
            <span class="col-span-2" v-if="food.data.editable">Current</span>
            <!-- <span v-if="!food.data.editable"></span> -->
            <span class="col-span-8" v-if="food.data.editable">Recommended</span>
            <!-- <span v-if="!food.data.editable"></span> -->

            <p class="col-span-12" v-if="errors.kcal">{{errors.kcal}}</p>
            <label class="col-span-2" for="kcal">KCal:</label>
            <input class="border rounded" id="kcal" type="number" :readonly="!food.data.editable" v-model="food.data.kcal" min="0"/>
            <div v-if="food.data.editable"></div>
            <input v-if="food.data.editable" class="border rounded" id="calc_kcal" type="number" readonly v-model="calculatedKCal">
            <div class="col-span-9" v-if="!food.data.editable"></div>
            <div class="col-span-7" v-if="food.data.editable"></div>

            <p class="col-span-12" v-if="errors.protein">{{errors.protein}}</p>
            <label class="col-span-2" for="protein">Protein:</label>
            <input class="border rounded" id="protein" type="number" :readonly="!food.data.editable" v-model="food.data.protein" min="0"/>
            <div v-if="food.data.editable"></div>
            <input v-if="food.data.editable" class="border rounded" id="calc_protein" type="number" readonly v-model="calculatedProtein">
            <div class="col-span-9" v-if="!food.data.editable"></div>
            <div class="col-span-7" v-if="food.data.editable"></div>

            <p class="col-span-12" v-if="errors.fat">{{errors.fat}}</p>
            <label class="col-span-2" for="fat">Fat:</label>
            <input class="border rounded" id="fat" type="number" :readonly="!food.data.editable" v-model="food.data.fat" min="0"/>
            <div v-if="food.data.editable"></div>
            <input v-if="food.data.editable" class="border rounded" id="calc_fat" type="number" readonly v-model="calculatedFat">
            <div class="col-span-9" v-if="!food.data.editable"></div>
            <div class="col-span-7" v-if="food.data.editable"></div>

            <p class="col-span-12" v-if="errors.carbohydrate">{{errors.carbohydrate}}</p>
            <label class="col-span-2" for="carbohydrate">Carbohydrate:</label>
            <input class="border rounded" id="carbohydrate" type="number" :readonly="!food.data.editable" v-model="food.data.carbohydrate" min="0"/>
            <div v-if="food.data.editable"></div>
            <input v-if="food.data.editable" class="border rounded" id="calc_carbohydrate" type="number" readonly v-model="calculatedCarbohydrate">
            <div class="col-span-9" v-if="!food.data.editable"></div>
            <div class="col-span-7" v-if="food.data.editable"></div>

            <p class="col-span-12" v-if="errors.potassium">{{errors.potassium}}</p>
            <label class="col-span-2" for="potassium">Potassium:</label>
            <input class="border rounded" id="potassium" type="number" :readonly="!food.data.editable" v-model="food.data.potassium" min="0"/>
            <div v-if="food.data.editable"></div>
            <input v-if="food.data.editable" class="border rounded" id="calc_potassium" type="number" readonly v-model="calculatedPotassium">
            <div class="col-span-9" v-if="!food.data.editable"></div>
            <div class="col-span-7" v-if="food.data.editable"></div>

            <p class="col-span-12" v-if="errors.base_quantity">{{errors.base_quantity}}</p>
            <label class="col-span-2" for="base_quantity">Base Quantity:</label>
            <input class="border rounded" id="base_quantity" type="number" :readonly="!food.data.editable" v-model="food.data.base_quantity" min="0"/>
            <div v-if="food.data.editable"></div>
            <input v-if="food.data.editable" class="border rounded" id="calc_base_quantity" type="number" readonly v-model="calculatedBaseQuantity">
            <div class="col-span-9" v-if="!food.data.editable"></div>
            <div class="col-span-7" v-if="food.data.editable"></div>

        </div>

        <div class="grid grid-cols-12 my-4">
            <div class="col-span-5 flex justify-between" v-if="food.data.editable">
                <button class="border rounded px-2" @click="updateFood">Update Food</button>
                <button class="border rounded px-2" @click="setToRecommendedValues">Set to Recommended Values</button>
            </div>
        </div>

        <ingredients-list
            v-if="food.data.editable"
            :food=food.data
            :foodgroups="foodgroups"
            :foods="foods"
        />
      </div>
    </app-layout>

</template>

<script>
import IngredientsList from "@/Shared/IngredientsList";
import Modal from "@/Shared/Modal";
import AppLayout from '@/Layouts/AppLayout';

export default {
    components:{
        AppLayout,
        IngredientsList,
        Modal
    },
    props:{
        food: Object,
        foods: Object,
        foodgroups: Object,
        errors: Object,
    },
    data(){
        return {
            iAmShowing:false,
            calculatedKCal: 0,
            calculatedFat: 0,
            calculatedProtein: 0,
            calculatedCarbohydrate: 0,
            calculatedPotassium: 0,
            calculatedBaseQuantity: 0
        }
    },
    mounted ()
    {
        this.calculatedKCal = Math.round(this.food.data.ingredients.reduce((total,ingredient)=>{
            return total+ingredient.kcal;
        }, 0));
        this.calculatedFat = Math.round(this.food.data.ingredients.reduce((total,ingredient)=>{
            return total+ingredient.fat;
        }, 0));
        this.calculatedProtein = Math.round(this.food.data.ingredients.reduce((total,ingredient)=>{
            return total+ingredient.protein;
        }, 0));
        this.calculatedCarbohydrate = Math.round(this.food.data.ingredients.reduce((total,ingredient)=>{
            return total+ingredient.carbohydrate;
        }, 0));
        this.calculatedPotassium = Math.round(this.food.data.ingredients.reduce((total,ingredient)=>{
            return total+ingredient.potassium;
        }, 0));
        this.calculatedBaseQuantity = Math.round(this.food.data.ingredients.reduce((total,ingredient)=>{
            return total+ingredient.quantity;
        }, 0));
    },
    methods:{
        cancelFoodUpdate () {
            let url = `${this.$route("foods.index")}`;
                this.$inertia.visit(url, {
                    // preserveState: true,
                    preserveScroll: true,
                });
        },
        updateFood () {
            this.$inertia.patch(
                this.$route("foods.update", {
                    'food': this.food.data.id,
                }), this.food.data
            );
        },
        setToRecommendedValues(){
            this.food.data.kcal=this.calculatedKCal;
            this.food.data.fat=this.calculatedFat;
            this.food.data.protein=this.calculatedProtein;
            this.food.data.carbohydrate=this.calculatedCarbohydrate;
            this.food.data.potassium=this.calculatedPotassium;
            this.food.data.base_quantity=this.calculatedBaseQuantity;
        }
    }
}
</script>
