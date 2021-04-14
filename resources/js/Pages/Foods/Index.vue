<template>
    <app-layout>
        <template #header>
            <div class="flex justify-between">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Foods
                </h2>
                <button @click="add">+</button>
            </div>
        </template>
        <div class="max-w-7xl mx-auto pb-2 px-4 sm:px-6 lg:px-8">
            <section class="py-2 grid grid-cols-12 gap-2">
                <label for="aliasSearch">Alias:</label>
                <input class="border col-span-3" type="text" name="aliasSearch" id="aliasSearch" @input="goToPageOne" v-model="aliasSearchText" placeholder="Alias"/>
                <div class="col-span-8"></div>

                <label for="descriptionSearch">Description:</label>
                <input class="border col-span-3" type="text" name="descriptionSearch" id="descriptionSearch" @input="goToPageOne" v-model="descriptionSearchText" placeholder="Description"/>
                <div class="col-span-8"></div>

                <label for="foodgroups">Food Group:</label>
                <select class="border col-span-3" name="foodgroups" id="foodgroups" v-model="foodgroupFilter" @change="goToPageOne">
                    <option value="">All</option>
                    <option v-for="foodgroup in foodgroups.data" :key="foodgroup.id" :value="foodgroup.id">
                        {{ foodgroup.description }}
                    </option>
                </select>
                <div class="col-span-8"></div>

                <div class="flex">
                    <p>Favourites:</p>
                </div>
                <div class="ml-2 col-span-2">
                    <label for="favouriteYes">Yes</label>
                    <input type="radio" name="favourites" id="favouriteYes" value="yes" v-model="favouritesFilter" @change="goToPageOne">
                    <label for="favouriteNo">No</label>
                    <input type="radio" name="favourites" id="favouriteNo" value="no" checked v-model="favouritesFilter" @change="goToPageOne">
                </div>
                <div class="col-span-9"></div>
            </section>
            <section class="py-2">
                <main-food-list :foods="foods.data" @view="show" @edit="show" @destroy="destroy" @favourite="toggleFavourite"></main-food-list>
                <div class="grid grid-cols-12 gap-2 mt-2">
                    <button class="col-span-1 border rounded" @click="goToPageOne">First</button>
                    <button class="col-span-1 border rounded" @click="previousPage">Previous</button>
                    <button class="col-span-1 border rounded" @click="nextPage">Next</button>
                    <button class="col-span-1 border rounded" @click="lastPage">Last</button>
                    <p class="col-span-2">Page: {{foods.meta.current_page}} of {{foods.meta.last_page}}</p>
                </div>
            </section>
        </div>
    </app-layout>
</template>

<script>
    import MainFoodList from "@/Shared/MainFoodList";
    import AppLayout from '@/Layouts/AppLayout';

    export default {
        components:{
            MainFoodList,
            AppLayout
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
            destroy(food){
                console.log("destroy", food.id);
                let url =this.$route("foods.destroy", food.id);
                this.$inertia.delete(url);
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
            show(food){
                let url = `${this.$route("foods.show", food.id)}`;
                // let url = `${this.$route("foods.show", e.target.id)}`;
                this.$inertia.visit(url);
            },
            add(){
                let url = `${this.$route("foods.create")}`;
                this.$inertia.visit(url);
            }
        }
    };
</script>
