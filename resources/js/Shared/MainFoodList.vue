<template>
  <div>
      <table class="table-fixed w-full mt-16">
            <tr>
                <th>Favourite</th>
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
            <tr v-for="food in foods" :key="food.id" class="odd:bg-gray-100">
                <td>
                    <input
                        type="checkbox"
                        :id="food.id"
                        :value="food.favourite"
                        :checked="food.favourite"
                        @change="favourite"
                    />
                </td>
                <td>{{food.alias}}</td>
                <td class="truncate">{{food.description}}</td>
                <td>{{food.kcal}}</td>
                <td>{{food.protein}}</td>
                <td>{{food.fat}}</td>
                <td>{{food.carbohydrate}}</td>
                <td>{{food.potassium}}</td>
                <td>{{food.base_quantity}}</td>
                <td>
                    <button v-if="food.editable" @click="edit" :id="food.id">
                        Edit
                    </button>
                    <button v-if="food.editable" @click="destroy" :id="food.id">
                        Delete
                    </button>
                    <button v-if="!food.editable" @click="view" :id="food.id">
                        View
                    </button>
                </td>
            </tr>
        </table>
  </div>
</template>

<script>
export default {
    props:{
        foods: Array,
    },
    methods:{
        favourite (e) {
            this.$emit('favourite', e);
        },
        view (e) {
            this.$emit('view', e);
        },
        edit (e) {
            this.$emit('edit', e);
        },
        destroy (e) {
            this.$emit('destroy', e);
        }
    }
}
</script>
<style scoped>
    td {
        padding:0.5rem;
    }
</style>
