<template>
    <div class="container">
        <h1>Foods</h1>
        <button @click="add">Add</button>
        <br/>
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
        <main-food-list :foods="foods.data" @view="show" @edit="show" @favourite="toggleFavourite"></main-food-list>
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
    import MainFoodList from "@/Shared/MainFoodList";
    export default {
        components:{
            MainFoodList,
        },
        props:{
            foods: Object,
            foodgroups: Object,
            page: Number
        },
        data() {
            return {
                descriptionSearchText: '',
                aliasSearchText: '',
                foodgroupFilter: '',
                favouritesFilter: '',
            }
        },
        methods:{
            toggleFavourite(e){
                let url =this.$route("foods.toggle-favourite", e.target.id);
                this.$inertia.post(url,{},{preserveScroll: true});
            },
            goToPageOne(){
                this.goToPage(1);
            },
            previousPage(){
                if(this.page>1) this.goToPage(this.page-1);
            },
            nextPage(){
                if(this.page<this.foods.meta.last_page) this.goToPage(this.page+1);
            },
            lastPage(){
                this.goToPage(this.foods.meta.last_page);
            },
            goToPage(page){
                let url = `${this.$route("foods.index")}`;
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
            show(e){
                let url = `${this.$route("foods.show", e.target.id)}`;
                this.$inertia.visit(url);
            },
            add(){
                let url = `${this.$route("foods.create")}`;
                this.$inertia.visit(url);
            }
        }
    };
</script>
