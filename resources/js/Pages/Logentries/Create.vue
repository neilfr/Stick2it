<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Add Log Entry
            </h2>
        </template>
        <div>
            <h1>Create Log Entry</h1>
            <form method="POST" @submit.prevent="submit">
                <div class="grid grid-cols-2 gap-2">
                    <p class="col-span-2" v-if="errors.consumed_at">{{errors.consumed_at}}</p>
                    <label class="p-2" for="consumed_at">Consumed at:</label>
                    <input class="border rounded" id="consumed_at_date" type="date" v-model="logentry.consumed_at">

                    <p class="col-span-2" v-if="errors.quantity">{{errors.quantity}}</p>
                    <label class="p-2" for="quantity">Quantity:</label>
                    <input class="border rounded" id="quantity" type="number" min="0" v-model="logentry.quantity" @change="updateQuantity">

                    <p class="col-span-2" v-if="errors.description">{{errors.consumed_at}}</p>
                    <label class="p-2" for="description">Description:</label>
                    <input disabled class="border rounded" id="description" type="text" :value="this.selectedFood.description">

                    <p class="col-span-2" v-if="errors.kcal">{{errors.kcal}}</p>
                    <label class="p-2" for="kcal">KCal:</label>
                    <input disabled class="border rounded" id="kcal" type="number" min="0" :value="logentry.kcal">

                    <p class="col-span-2" v-if="errors.fat">{{errors.fat}}</p>
                    <label class="p-2" for="fat">Fat:</label>
                    <input disabled class="border rounded" id="fat" type="number" min="0" :value="logentry.fat">

                    <p class="col-span-2" v-if="errors.protein">{{errors.protein}}</p>
                    <label class="p-2" for="protein">Protein:</label>
                    <input disabled class="border rounded" id="protein" type="number" min="0" :value="logentry.protein">

                    <p class="col-span-2" v-if="errors.carbohydrate">{{errors.carbohydrate}}</p>
                    <label class="p-2" for="carbohydrate">Carbohydrate:</label>
                    <input disabled class="border rounded" id="carbohydrate" type="number" min="0" :value="logentry.carbohydrate">

                    <p class="col-span-2" v-if="errors.potassium">{{errors.potassium}}</p>
                    <label class="p-2" for="potassium">Potassium:</label>
                    <input disabled class="border rounded" id="potassium" type="number" min="0" :value="logentry.potassium">

                </div>
            </form>
            <button @click='cancel'>Cancel</button>
            <button :disabled="!readyToSave" @click="store">Save</button>
            <button @click="handlePickFood">Pick Food</button>
            <div v-if="showSelectFoodModal" class="fixed inset-0 w-full h-screen flex items-center justify-center overflow-auto">
                <div class="w-full max-w-6xl bg-white shadow-lg rounded-lg p-8">
                <label for="foodgroups">Food Group:</label>
                <select name="foodgroups" id="foodgroups" v-model="foodgroupFilter" @change="goToPageOne">
                    <option value="">All</option>
                    <option v-for="foodgroup in foodgroups.data" :key="foodgroup.id" :value="foodgroup.id">
                        {{ foodgroup.description }}
                    </option>
                </select>
                <br>
                <label for="descriptionSearch">Description Search:</label>
                <input type="text" name="descriptionSearch" id="descriptionSearch" @input="goToPageOne" v-model="descriptionSearchText"/>
                <br/>
                <label for="aliasSearch">Alias Search:</label>
                <input type="text" name="aliasSearch" id="aliasSearch" @input="goToPageOne" v-model="aliasSearchText"/>
                <div class="flex">
                    <p>Favourites:</p>
                    <div class="ml-2">
                        <label for="favouriteYes">Yes</label>
                        <input type="radio" name="favourites" id="favouriteYes" value="yes" v-model="favouritesFilter" @change="goToPageOne">
                        <label for="favouriteNo">No</label>
                        <input type="radio" name="favourites" id="favouriteNo" value="no" checked v-model="favouritesFilter" @change="goToPageOne">
                    </div>
                </div>
                <table>
                    <tr>
                        <th>Favourite</th>
                        <th>Alias</th>
                        <th>Description</th>
                        <th>Base Quantity</th>
                        <th>KCal</th>
                        <th>Protein</th>
                        <th>Fat</th>
                        <th>Carbohydrate</th>
                        <th>Potassium</th>
                        <th>Quantity</th>
                        <th>Actions</th>
                    </tr>
                    <tr v-for="food in foods.data" :key="food.id">
                        <td>
                            <div class="ml-2">
                                <input type="checkbox" name="favourites" id="favourite" disabled :checked="food.favourite">
                            </div>
                        </td>
                        <td>{{food.alias}}</td>
                        <td>{{food.description}}</td>
                        <td>{{food.base_quantity}}</td>
                        <td>{{Math.round(food.kcal)}}</td>
                        <td>{{Math.round(food.protein)}}</td>
                        <td>{{Math.round(food.fat)}}</td>
                        <td>{{Math.round(food.carbohydrate)}}</td>
                        <td>{{Math.round(food.potassium)}}</td>
                        <td>{{food.quantity}}</td>
                        <td><button @click="selectFood(food)">Select</button></td>
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
            </div>
        </div>
    </app-layout>
</template>

<script>

import AppLayout from '@/Layouts/AppLayout';

export default {
    components: {
        AppLayout,
    },
    props: {
        errors: Object,
        user: Object,
        foods: Object,
        foodgroups: Object
    },
    computed: {
        readyToSave () {
            return this.logentry.quantity>0 && !isNaN(new Date(this.logentry.consumed_at).getDate()) && this.selectedFood!=null;
        }
    },
    data() {
        return {
            showSelectFoodModal: false,
            descriptionSearchText: '',
            aliasSearchText: '',
            foodgroupFilter: '',
            favouritesFilter: '',
            page: 1,
            logentry: {
                user_id: this.user.id,
                description: '',
                quantity: 0,
                kcal: 0,
                fat: 0,
                protein: 0,
                carbohydrate: 0,
                potassium: 0,
                consumed_at: (new Date()).toISOString().substr(0,10),
            },
            selectedFood: ''
        }
    },
    methods: {
        store(){
            console.log("payload",                {
                    'user_id': this.user.id,
                    'description': this.logentry.description,
                    'quantity': this.logentry.quantity,
                    'kcal': this.logentry.kcal,
                    'fat': this.logentry.fat,
                    'protein': this.logentry.protein,
                    'carbohydrate': this.logentry.carbohydrate,
                    'potassium': this.logentry.potassium,
                    'consumed_at': this.logentry.consumed_at
                });
            this.$inertia.post(
                this.$route("logentries.store"),
                {
                    'user_id': this.user.id,
                    'description': this.logentry.description,
                    'quantity': this.logentry.quantity,
                    'kcal': this.logentry.kcal,
                    'fat': this.logentry.fat,
                    'protein': this.logentry.protein,
                    'carbohydrate': this.logentry.carbohydrate,
                    'potassium': this.logentry.potassium,
                    'consumed_at': this.logentry.consumed_at
                }
            )
        },
        handlePickFood(){
            this.showSelectFoodModal = true;
        },
        selectFood(food){
            console.log("logentry.quantity", this.logentry.quantity);
            this.selectedFood=food;
            this.logentry.description=food.description;
            if(this.logentry.quantity===0){
                this.logentry.quantity = food.base_quantity
            }
            this.updateQuantity();
            this.showSelectFoodModal = false;
        },
        updateQuantity(){
            if(this.selectedFood){
                this.logentry.kcal = Math.round(this.selectedFood.kcal * (this.logentry.quantity / this.selectedFood.base_quantity));
                this.logentry.fat = Math.round(this.selectedFood.fat * (this.logentry.quantity / this.selectedFood.base_quantity));
                this.logentry.protein = Math.round(this.selectedFood.protein * (this.logentry.quantity / this.selectedFood.base_quantity));
                this.logentry.carbohydrate = Math.round(this.selectedFood.carbohydrate * (this.logentry.quantity / this.selectedFood.base_quantity));
                this.logentry.potassium = Math.round(this.selectedFood.potassium * (this.logentry.quantity / this.selectedFood.base_quantity));
            }
        },
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
            let url = `${this.$route("logentries.create")}`;
            url += `?descriptionSearch=${this.descriptionSearchText}`;
            url += `&aliasSearch=${this.aliasSearchText}`;
            url += `&foodgroupSearch=${this.foodgroupFilter}`;
            url += `&favouritesFilter=${this.favouritesFilter}`;
            this.$inertia.visit(url, {
                data:{
                    'page':this.page
                },
                preserveState: true,
                preserveScroll: true,
            });
        },
        cancel(){
            console.log("cancel");
            let url = `${this.$route("logentries.index")}`;
            this.$inertia.visit(url);
        }
    }
}
</script>
