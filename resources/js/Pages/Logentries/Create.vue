<template>
    <div>
        <h1>Create Log Entry</h1>
        <form method="POST" @submit.prevent="submit">
            <div class="grid grid-cols-2 gap-2">
                <p class="col-span-2" v-if="errors.quantity">{{errors.quantity}}</p>
                <label class="p-2" for="quantity">Quantity:</label>
                <input class="border rounded" id="quantity" type="number" min="0" v-model="logentry.quantity">
                <p class="col-span-2" v-if="errors.consumed_at">{{errors.consumed_at}}</p>
                <label class="p-2" for="consumed_at">Consumed at:</label>
                <input class="border rounded" id="consumed_at_date" type="date" v-model="logentry.consumed_at">
            </div>
        </form>
        <button>Cancel</button>
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
                    <td><button :disabled="!readyToSelect" @click="store(food)">Select</button><span>"{{readyToSelect}}"</span></td>
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
        foods: Object,
        foodgroups: Object
    },
    computed: {
        readyToSelect () {
            return this.logentry.quantity>0 &&  !isNaN(new Date(this.logentry.consumed_at).getDate());
        }
    },
    data() {
        return {
            descriptionSearchText: '',
            aliasSearchText: '',
            foodgroupFilter: '',
            favouritesFilter: '',
            page: 1,
            logentry: {
                user_id: this.user.id,
                food_id: 2,
                consumed_at: (new Date()).toISOString().substr(0,10),
                quantity: 0
            },
        }
    },
    methods: {
        store(food){
            console.log(food);
            this.$inertia.post(
                this.$route("logentries.store"),
                {
                    'user_id': this.user.id,
                    'food_id': food.id,
                    'quantity': this.logentry.quantity,
                    'consumed_at': this.logentry.consumed_at
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
    }
}
</script>
