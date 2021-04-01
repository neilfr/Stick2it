<template>
    <div>
        <h1>Edit Log Entry</h1>
        <form method="POST" @submit.prevent="submit">
            <div class="grid grid-cols-2 gap-2">
                <p class="col-span-2" v-if="errors.quantity">{{errors.quantity}}</p>
                <label class="p-2" for="quantity">Quantity:</label>
                <input class="border rounded" id="quantity" type="number" min="0" v-model="logentry.data.quantity">
                <p class="col-span-2" v-if="errors.consumed_at">{{errors.consumed_at}}</p>
                <label class="p-2" for="consumed_at">Consumed at:</label>
                <input class="border rounded" id="consumed_at_date" type="date" v-model="logentry.data.consumed_at">
                <label class="p-2" for="selected_food_alias">Food Alias:</label>
                <input disabled class="border rounded" id="selected_food_alias" type="text" :value="selectedFood.alias">
                <label class="p-2" for="selected_food_description">Food Description:</label>
                <input disabled class="border rounded" id="selected_food_description" type="text" :value="selectedFood.description">
            </div>
        </form>
        <div>
            <button @click="update">Save</button>
            <button>Cancel</button>
        </div>
        <div>
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
                <tr v-for="food in foods.data" :key="food.id">
                    <td>{{food.alias}}</td>
                    <td>{{food.description}}</td>
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
</template>

<script>
export default {
    props: {
        errors: Object,
        user: Object,
        logentry: Object,
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
            descriptionSearchText: '',
            aliasSearchText: '',
            foodgroupFilter: '',
            favouritesFilter: '',
            page: 1,
            selectedFood: this.logentry.data.food
        }
    },
    methods: {
        selectFood(food){
            console.log("food", food);
            this.selectedFood = food;
            console.log("selectedfood", this.selectedFood);
        },
        update(){
            console.log("update", this.logentry.data.id);
            console.log("withfood", this.selectedFood);
            console.log("withqty", this.logentry.data.quantity);
            console.log("withfood", this.logentry.data.consumed_at);

            this.$inertia.patch(
                this.$route("logentries.update", this.logentry.data.id),
                {
                    'user_id': this.logentry.data.user.id,
                    'food_id': this.selectedFood.id,
                    'quantity': this.logentry.data.quantity,
                    'consumed_at': this.logentry.data.consumed_at
                }
            )
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
            let url = `${this.$route("logentries.edit", this.logentry.data)}`;
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
    }
}
</script>
