<template>
    <app-layout>
        <template #header>
            <div class="flex justify-between">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Edit Log Entry
                </h2>
                <button @click="cancel">
                    <img class="w-6" src="/images/close.svg">
                </button>
            </div>
        </template>
        <div>
            <form method="POST" @submit.prevent="submit">
                <div class="grid grid-cols-12 gap-2">
                    <button class="border rounded col-span-2" :disabled="!readyToSave" @click="update">Save</button>
                    <button class="border rounded col-span-2" @click="handlePickFood">Pick Food</button>
                    <div class="col-span-8"></div>

                    <p class="col-span-12" v-if="errors.consumed_at">{{errors.consumed_at}}</p>
                    <label class="py-2 col-span-2" for="consumed_at">Consumed at:</label>
                    <input class="border rounded col-span-2" id="consumed_at_date" type="date" v-model="logentry.data.consumed_at">
                    <div class="col-span-8"></div>

                    <p class="col-span-12" v-if="errors.quantity">{{errors.quantity}}</p>
                    <label class="py-2 col-span-2" for="quantity">Quantity:</label>
                    <input class="border rounded col-span-2" id="quantity" type="number" min="0" v-model="logentry.data.quantity" @change="updateQuantity">
                    <div class="col-span-8"></div>

                    <p class="col-span-12" v-if="errors.description">{{errors.consumed_at}}</p>
                    <label class="py-2 col-span-2" for="description">Description:</label>
                    <input disabled class="border rounded col-span-2" id="description" type="text" :value="logentry.data.description">
                    <div class="col-span-8"></div>

                    <p class="col-span-12" v-if="errors.kcal">{{errors.kcal}}</p>
                    <label class="py-2 col-span-2" for="kcal">KCal:</label>
                    <input disabled class="border rounded col-span-2" id="kcal" type="number" min="0" :value="logentry.data.kcal">
                    <div class="col-span-8"></div>

                    <p class="col-span-12" v-if="errors.fat">{{errors.fat}}</p>
                    <label class="py-2 col-span-2" for="fat">Fat:</label>
                    <input disabled class="border rounded col-span-2" id="fat" type="number" min="0" :value="logentry.data.fat">
                    <div class="col-span-8"></div>

                    <p class="col-span-12" v-if="errors.protein">{{errors.protein}}</p>
                    <label class="py-2 col-span-2" for="protein">Protein:</label>
                    <input disabled class="border rounded col-span-2" id="protein" type="number" min="0" :value="logentry.data.protein">
                    <div class="col-span-8"></div>

                    <p class="col-span-12" v-if="errors.carbohydrate">{{errors.carbohydrate}}</p>
                    <label class="py-2 col-span-2" for="carbohydrate">Carbohydrate:</label>
                    <input disabled class="border rounded col-span-2" id="carbohydrate" type="number" min="0" :value="logentry.data.carbohydrate">
                    <div class="col-span-8"></div>

                    <p class="col-span-12" v-if="errors.potassium">{{errors.potassium}}</p>
                    <label class="py-2 col-span-2" for="potassium">Potassium:</label>
                    <input disabled class="border rounded col-span-2" id="potassium" type="number" min="0" :value="logentry.data.potassium">
                    <div class="col-span-8"></div>

                </div>
            </form>
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
        food: Object,
        foods: Object,
        foodgroups: Object,
        logentry: Object
    },
    computed: {
        readyToSave () {
            return this.logentry.data.quantity>0 && !isNaN(new Date(this.logentry.data.consumed_at).getDate()) && this.logentry.data.description!=null;
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
            selectedFood: this.food,
        }
    },
    methods: {
        update(){
            this.$inertia.patch(
                this.$route("logentries.update", this.logentry.data.id),
                {
                    'user_id': this.user.id,
                    'description': this.logentry.data.description,
                    'quantity': this.logentry.data.quantity,
                    'kcal': this.logentry.data.kcal,
                    'fat': this.logentry.data.fat,
                    'protein': this.logentry.data.protein,
                    'carbohydrate': this.logentry.data.carbohydrate,
                    'potassium': this.logentry.data.potassium,
                    'consumed_at': this.logentry.data.consumed_at
                }
            )
        },
        handlePickFood(){
            this.showSelectFoodModal = true;
        },
        selectFood(food){
            this.selectedFood=food;
            this.logentry.data.description=food.description;
            if(this.logentry.data.quantity===0){
                this.logentry.data.quantity = food.base_quantity
            }
            this.updateQuantity();
            this.showSelectFoodModal = false;
        },
        updateQuantity(){
            if(this.selectedFood){
                this.logentry.data.kcal = Math.round(this.selectedFood.kcal * (this.logentry.data.quantity / this.selectedFood.base_quantity));
                this.logentry.data.fat = Math.round(this.selectedFood.fat * (this.logentry.data.quantity / this.selectedFood.base_quantity));
                this.logentry.data.protein = Math.round(this.selectedFood.protein * (this.logentry.data.quantity / this.selectedFood.base_quantity));
                this.logentry.data.carbohydrate = Math.round(this.selectedFood.carbohydrate * (this.logentry.data.quantity / this.selectedFood.base_quantity));
                this.logentry.data.potassium = Math.round(this.selectedFood.potassium * (this.logentry.data.quantity / this.selectedFood.base_quantity));
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
            let url = `${this.$route("logentries.edit", this.logentry.data.id)}`;
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
