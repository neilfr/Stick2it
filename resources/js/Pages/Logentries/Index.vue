<template>
    <app-layout>
        <template #header>
            <div class="flex justify-between">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Log Entries
                </h2>
                <button @click="add">
                    <img class="w-6" src="/images/add-outline.svg">
                </button>
            </div>
        </template>
        <div class="container">
            <div class="grid grid-cols-12 gap-2">
                <div class="col-span-4 grid grid-cols-4 place-items-start border p-2">
                    <h3 class="col-span-4 font-bold text-l text-gray-800 my-2">Period</h3>
                    <label for="from">From:</label>
                    <input class="col-span-3 border mr-16 mb-2" type="date" name="from" id="from" v-model='from' @change="goToPageOne">
                    <label for="to">To:</label>
                    <input class="col-span-3 border mr-16" type="date" name="to" id="to" v-model='to' @change="goToPageOne">
                    <div class="col-span-3"><br/></div>
                    <div class="col-span-3"><br/></div>
                    <div class="col-span-3"><br/></div>

                </div>
                <div class="col-span-4 grid grid-cols-2 border p-2">
                    <h1 class="col-span-2 font-bold text-l text-gray-800 my-2">Totals for Period</h1>
                    <label for="totalKcal">Calories</label>
                    <input type="text" name="totalKcal" id="totalKcal" :value="totalKcal" disabled>

                    <label for="totalFat">Fat</label>
                    <input type="text" name="totalFat" id="totalFat" :value="totalFat" disabled>

                    <label for="totalProtein">Protein</label>
                    <input type="text" name="totalProtein" id="totalProtein" :value="totalProtein" disabled>

                    <label for="totalCarbohydrate">Carbohydrate</label>
                    <input type="text" name="totalCarbohydrate" id="totalCarbohydrate" :value="totalCarbohydrate" disabled>

                    <label for="totalPotassium">Potassium</label>
                    <input type="text" name="totalPotassium" id="totalPotassium" :value="totalPotassium" disabled>
                </div>
                <div class="col-span-4 grid grid-cols-2 border p-2">
                    <h1 class="col-span-2 font-bold text-l text-gray-800 my-2">Averages for Period</h1>
                    <label for="averageKcal">Calories</label>
                    <input type="text" name="averageKcal" id="averageKcal" :value="averageKcal" disabled>

                    <label for="averageFat">Fat</label>
                    <input type="text" name="averageFat" id="averageFat" :value="averageFat" disabled>

                    <label for="averageProtein">Protein</label>
                    <input type="text" name="averageProtein" id="averageProtein" :value="averageProtein" disabled>

                    <label for="averageCarbohydrate">Carbohydrate</label>
                    <input type="text" name="averageCarbohydrate" id="averageCarbohydrate" :value="averageCarbohydrate" disabled>

                    <label for="averagePotassium">Potassium</label>
                    <input type="text" name="averagePotassium" id="averagePotassium" :value="averagePotassium" disabled>
                </div>
            </div>
            <table class="table-fixed w-full mt-16">
                <tr>
                    <th class="w-1/6">Date / Time</th>
                    <th class="w-1/3">Description</th>
                    <th class="text-left translate-x-4 transform -rotate-45 origin-bottom-left">Quantity</th>
                    <th class="text-left translate-x-4 transform -rotate-45 origin-bottom-left">KCal</th>
                    <th class="text-left translate-x-4 transform -rotate-45 origin-bottom-left">Fat</th>
                    <th class="text-left translate-x-4 transform -rotate-45 origin-bottom-left">Protein</th>
                    <th class="text-left translate-x-4 transform -rotate-45 origin-bottom-left">Carbohydrate</th>
                    <th class="text-left translate-x-4 transform -rotate-45 origin-bottom-left">Potassium</th>
                    <th class="text-left translate-x-4 transform -rotate-45 origin-bottom-left">Actions</th>
                </tr>
                <tr v-for="logentry in logentries.data" :key="logentry.id" class="odd:bg-gray-100 leading-9">
                    <td class="text-center">{{logentry.consumed_at}}</td>
                    <td class="truncate">{{logentry.food.description}}</td>
                    <td>{{logentry.quantity}}</td>
                    <td>{{logentry.kcal}}</td>
                    <td>{{logentry.fat}}</td>
                    <td>{{logentry.protein}}</td>
                    <td>{{logentry.carbohydrate}}</td>
                    <td>{{logentry.potassium}}</td>
                    <td class="text-center flex justify-between">
                        <button @click="edit(logentry)">
                            <img class="w-6" src="/images/edit-pencil.svg">
                        </button>
                        <button @click="destroy(logentry)">
                            <img class="w-6" src="/images/trash.svg">
                        </button>
                    </td>
                </tr>
            </table>
            <div class="grid grid-cols-12 gap-2 mt-2">
                <button class="col-span-1 border rounded" @click="goToPageOne">First</button>
                <button class="col-span-1 border rounded" @click="previousPage">Previous</button>
                <button class="col-span-1 border rounded" @click="nextPage">Next</button>
                <button class="col-span-1 border rounded" @click="lastPage">Last</button>
                <p class="col-span-2">Page: {{logentries.meta.current_page}} of {{logentries.meta.last_page}}</p>
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
    props:{
        logentries: Object,
        page: Number,
        totalKcal: Number,
        totalFat: Number,
        totalProtein: Number,
        totalCarbohydrate: Number,
        totalPotassium: Number,
        averageKcal: Number,
        averageFat: Number,
        averageProtein: Number,
        averageCarbohydrate: Number,
        averagePotassium: Number,
    },
    data(){
        return {
            today: '',
            from: '',
            to: '',
            myhello:''
        }
    },
    methods:{
        add () {
            let url = `${this.$route("logentries.create")}`;
            this.$inertia.visit(url);
        },
        edit(logentry) {
            let url = `${this.$route("logentries.edit", logentry)}`;
            this.$inertia.visit(url);
        },
        destroy(logentry) {
            let url = this.$route("logentries.destroy", logentry.id);
            this.$inertia.delete(url);
        },
        goToPageOne(){
            this.goToPage(1);
        },
        previousPage(){
            if(this.page>1) this.goToPage(this.page-1);
        },
        nextPage(){
            if(this.page<this.logentries.meta.last_page) this.goToPage(this.page+1);
        },
        lastPage(){
            this.goToPage(this.logentries.meta.last_page);
        },
        goToPage(page){
            let url = `${this.$route("logentries.index")}`;
            url += `?from=${this.from}`;
            url += `&to=${this.to}`;
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
