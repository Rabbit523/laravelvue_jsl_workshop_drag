<template>
  <div>
    <c-panel :title="pageTitle">
            <c-form layout="horizontal" span="220" @submit.prevent="handleSubmit">

        <c-form-field label="Customer Name">
          <c-form-input 
              v-validate="''" 
              v-model="form.customerName" 
              :status="checkErrors('customer name')" 
              name="customer name" 
              value="" 
              type="text">
          </c-form-input>
          <span class="form-help u-color-danger">{{ errors.first('customer name') }}</span>
        </c-form-field> 

        <c-form-field label="Order Number">
          <c-form-input 
              v-validate="''" 
              v-model="form.orderNumber" 
              :status="checkErrors('order number')" 
              name="order number" 
              value="" 
              type="text">
          </c-form-input>
          <span class="form-help u-color-danger">{{ errors.first('order number') }}</span>
        </c-form-field> 

        <c-form-field label="Suburb">
        <c-multiselect
            v-model="suburb"
            v-validate="'required'"
            track-by="value"
            label="label"
            :options="locations"
            name="locations"
            :searchable="true"
            :close-on-select="true"
            :multiple="false"
            placeholder="Select Suburb"></c-multiselect>
          <span class="form-help u-color-danger">{{ errors.first('suburb') }}</span>
        </c-form-field>

        

        <c-form-field label="Description" v-if="form.suburb != undefined">
          <c-form-input 
              v-validate="''" 
              v-model="form.description" 
              :status="checkErrors('description')" 
              name="description" 
              value="" 
              type="text">
          </c-form-input>
          <span class="form-help u-color-danger">{{ errors.first('description') }}</span>
        </c-form-field>

        

        <c-form-field label="Clear Access Available?" v-if="form.suburb != undefined">
          <c-form-radio name="clearAccess" v-model="form.clearAccess" value="yes" label="Yes" /></c-form-radio>
          <c-form-radio name="clearAccess" v-model="form.clearAccess" value="no" label="No" /></c-form-radio>
        </c-form-field>

        <c-form-field label="Delivery Location" v-if="form.clearAccess == 'no'">
        <c-multiselect
            v-model="form.deliveryLocation"
            v-validate="'required'"
            track-by="value"
            label="label"
            :options="noClearAccess"
            name="delivery location"
            :searchable="true"
            :close-on-select="true"
            :multiple="false"
            placeholder="Select Delivery Location"></c-multiselect>
          <span class="form-help u-color-danger">{{ errors.first('delivery location') }}</span>
        </c-form-field>

        <c-form-field label="Is it upstairs?" v-if="form.clearAccess != undefined">
          <c-form-radio name="upstairs" v-model="upstairs" value="no" label="No" /></c-form-radio>
          <c-form-radio name="upstairs" v-model="upstairs" value="yes" label="Yes" /></c-form-radio>
        </c-form-field>

        <c-form-field v-if="form.upstairs == 'yes'">
          <c-form-checkbox label="Charged for upstairs?" value="yes" v-validate="'required'" v-model="form.chargeForUpstair"></c-form-checkbox>
        </c-form-field>

        <c-form-field label="Booking Type" v-if="form.upstairs != undefined">
        <c-multiselect
            v-model="form.bookingType"
            v-validate="'required'"
            track-by="value"
            label="label"
            :options="bookingTypeOptions"
            name="booking type"
            :searchable="true"
            :close-on-select="true"
            :multiple="false"
            placeholder="Select Booking Type"></c-multiselect>
          <span class="form-help u-color-danger">{{ errors.first('booking type') }}</span>
        </c-form-field>

        <c-form-field label="Duration (Delivery + Assembly)" v-if="form.bookingType != undefined">
          <c-multiselect
              v-model="duration"
              v-validate="'required'"
              track-by="value"
              label="label"
              :options="durationOptions"
              name="duration"
              :searchable="true"
              :close-on-select="true"
              :multiple="false"
              placeholder="Select Duration"></c-multiselect>
          <span class="form-help u-color-danger">{{ errors.first('duration') }}</span>
        </c-form-field>

         <c-form-field label="Delivery Slot" v-if="form.duration != undefined">
          <c-multiselect
              v-model="form.deliverySlot"
              v-validate="'required'"
              track-by="value"
              label="label"
              :options="deliverySlots"
              name="delivery slot"
              :searchable="true"
              :close-on-select="true"
              :multiple="false"
              placeholder="Select Slot"></c-multiselect>
          <span class="form-help u-color-danger">{{ errors.first('delivery slot') }}</span>
        </c-form-field>

      </c-form>
    </c-panel>

    <c-panel>
      <c-row>
        <c-col order="1">
          <c-panel title="One Man">
            <!-- Just for development purpose. You can remove this anytime -->
            <div class="booking-placement-output" id="booking-placement-output" style="text-align:center;padding: 15px;">
              Data on Drop: <span style="color: red;">{{dataOutput}}</span>
              <br>
              Message on Drop: <span style="color: red;">{{bookingDroppedMSG}}</span>
            </div>
            <table class="table table--bordered">
              <thead>
                <tr>
                  <th class="u-text-center">Time Slot</th>
                  <th class="day-head-1 u-text-center" data-day="Monday">Monday</th>
                  <th class="day-head-2 u-text-center" data-day="Tuesday">Tuesday</th>
                  <th class="day-head-3 u-text-center" data-day="Wedneday">Wedneday</th>
                  <th class="day-head-4 u-text-center" data-day="Thursday">Thursday</th>
                  <th class="day-head-5 u-text-center" data-day="Friday">Friday</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(slot, slotIndex) in pairedSlots" v-bind:key="slotIndex">
                  <td
                  :id="'time-slot-' + slotIndex"
                  >{{ slot.label }}</td>
                    <!-- :rowspan="week.day.slotsTaken" -->
<!-- :class="[
                      week.day.slotColor,
                      'week-day-'+week.index+slotIndex,
                      bookingState(this, week.day),
                      focus.slot == week.day.id ? 'u-bg-success' : 'u-bg-primary'
                    ]" -->
                  <td
                    class="u-text-center td-dropzone"
                    :key="'week-day-'+weekIndex"
                    :id="'slot-'+week.day.id"
                    :data-id="week.day.id"
                    :data-count="week.day.slotsTaken"
                    :data-index="slotIndex"
                    :data-column="week.index"
                    :data-color="week.day.slotColor"
                    :class="[
                      'week-day-'+week.index+slotIndex,
                      bookingState(week)
                    ]"
                    @click="focusSlot(week)"
                    @drag-start="handleDragStart"
                    @drag-over="handleDragOver"
                    @drag-enter="handleDragEnter"
                    @drag-leave="handleDragLeave"
                    @drag-end="handleDragEnd"
                    @drop="handleDrop"
                    @drag="handleDrag"
                    v-drag-and-drop
                    v-for="(week, weekIndex) in slot.week"
                  >
                    <div
                      class="booking"
                      :id="'booking-'+week.day.booking.id+week.index+slotIndex"
                      :data-id="week.day.booking.id"
                      :data-count="week.day.slotsTaken"
                      :data-column="week.index"
                      :data-slot-id="week.day.id"
                      :data-booking-index="slotIndex"
                      :class="[
                        'booking-'+week.day.booking.id + week.index+slotIndex,
                        'booking-movable',
                        focus.booking == week.day.booking.id ? 'u-bg-success' : 'u-bg-danger'
                      ]"
                      @drag="handleDrag"
                      @drop="handleBookingDrop"
                      v-drag-and-drop
                      v-if="week.day.booking"
                    >
                      {{ week.day.booking.areaName }}
                      <br>
                      {{ week.day.booking.customerName }}
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </c-panel>
        </c-col>
      </c-row>
    </c-panel>
    <c-form-field>
      <c-dropdown>
        <c-button :loading="form.isLoading" :disabled="form.isLoading" type="primary">Save</c-button>
        <c-dropmenu :hidden="form.isLoading" slot="content">
          <c-dropmenu-item @click="handleSubmit('save')" icon="icon-new" title="Save"/>
          <c-dropmenu-item
            @click="handleSubmit('save&new')"
            icon="icon-new"
            title="Save &amp; Add New"
          />
          <c-dropmenu-item
            @click="handleSubmit('save&close')"
            icon="icon-list"
            title="Save &amp; View All"
          />
        </c-dropmenu>
      </c-dropdown>
    </c-form-field>
  </div>
</template>
<style scoped>
  .td-dropzone {
    padding: 0px !important;
  }
  .td-dropzone.slot-dropzone {
    background: rgb(214, 210, 149) !important;
    padding: 0px !important;
  }
  .booking {
    padding: 8px 16px;
    height: 100% !important;
    color: #fff;
    cursor: pointer;
  }
</style>
<script>
  import { format, addDays } from "date-fns";
  import { Form, Errors } from "./../common/base.js";

  export default {
    data() {
      return {
        form: new Form({
          customerName: "",
          orderNumber: "",
          suburb: undefined,
          description: undefined,
          clearAccess: undefined,
          upstairs: undefined,
          chargeForUpstair: undefined,
          bookingType: undefined,
          deliveryLocation: undefined,
          bookingDate: undefined,
          bookingTime: undefined,
          deliverySlot: undefined,
          duration: undefined,
          status: ""
        }),
        bookingDate: undefined,
        upstairs: undefined,
        duration: undefined,
        dateArray: [],
        suburb: undefined,
        locations: [],

        groupOptions: [
          { label: "Yes", name: "yes" },
          { label: "No", name: "no" }
        ],

        noClearAccess: [
          { label: "Deliver to garage", value: "Deliver to garage" },
          { label: "Deliver to doorstep", value: "Deliver to doorstep" },
          { label: "Other", value: "Other" }
        ],

        durationOptions: [
          { label: "30min", value: "1" },
          { label: "1 Hour", value: "2" },
          { label: "1.5 Hours", value: "3" },
          { label: "2 Hours", value: "4" },
          { label: "2.5 Hours", value: "5" },
          { label: "3 Hours", value: "6" },
          { label: "3.5 Hours", value: "7" },
          { label: "4 Hours", value: "8" },
          { label: "4.5 Hours", value: "9" },
          { label: "5 Hours", value: "10" },
          { label: "5.5 Hours", value: "11" },
          { label: "6 Hours", value: "12" },
          { label: "6.5 Hours", value: "13" },
          { label: "Full Day", value: "14" }
        ],

        bookingTypeOptions: [
          { label: "One Man", name: "One Man", value: "One Man" },
          { label: "Two Man", name: "Two Man", value: "Two Man" }
        ],

        hours: [
          { label: "09:00 - 09:30" },
          { label: "09:30 - 10:00" },
          { label: "10:00 - 10:30" },
          { label: "10:30 - 11:00" },
          { label: "11:00 - 11:30" },
          { label: "11:30 - 12:00" },
          { label: "12:00 - 12:30" },
          { label: "12:30 - 01:00" },
          { label: "01:00 - 01:30" },
          { label: "01:30 - 02:00" },
          { label: "02:00 - 02:30" },
          { label: "02:30 - 03:00" },
          { label: "03:00 - 03:30" },
          { label: "03:30 - 04:00" }
        ],

        bookings: [],

        deliverySlots: [],
        todayDate: undefined,

        pageTitle: "Booking > Add New",
        api: "",
        controller: "/booking/",
        controllerName: "Booking",
        basePost: "",
        postUrl: "",
        pageid: "",

        toast: "",
        // output json container
        dataOutput: 'Please drag and drop any booking to see the result.',
        bookingDroppedMSG : 'Please drag and drop any booking to see the result.',
        table: {
          bookings: [],
          slots: [],
          filterBy: "One Man",
          loaded : false
        },
        pairedSlots: [],
        focus: {booking: null, slot: null},
      };
    },

    components: {},

    created() {
      this.basePost = this.api + this.controller;
      this.postUrl = this.basePost;

      this.bookingDate = format(new Date(), "DD-MM-YYYY");
      // this.bookingDate = '28-01-2019';

      if (this.$route.params.id) {
        this.loading("Loading");
        this.getData(this.$route.params.id);
        this.isDisabled = true;
      }

      // this.createQuantity();
      this.getLocations();
      // this.getClients();
    },
    
    mounted: function () {
      setInterval(() => {
        
      }, 100);

    },
    
    methods: {
      /******************************************************************************************
       *                           Drag and drop methods starts from here
       ******************************************************************************************/
      handleDragStart: function(e) {
        // this.loggedEvent = "handleDragStart";
        // console.log(this.loggedEvent, e);
      },
      handleDragOver: function(e) {
        this.loggedEvent = "handleDragOver";
        console.log(this.loggedEvent, e);
      },
      handleDragEnter: function(e) {
        // this.loggedEvent = "handleDragEnter";
        // console.log(this.loggedEvent, e);
      },
      handleDragLeave: function(e) {
        // this.loggedEvent = "handleDragLeave";
        // console.log(this.loggedEvent, e);
      },
      handleDragEnd: function(e) {
        // this.loggedEvent = "handleDragEnd";
        // console.log(this.loggedEvent, e);
      },
      handleDrop: function(e) {
        this.loggedEvent = "handleDrop";
        let el = this.eObject(e);
        if (el.booking[0].tagName === 'DIV' && !this.isSlotHasAlreadyBooked(el)) {
            this.handleDropBookingIntoCell(el);
          }
        this.currentlyDragging = null;
      },
      handleBookingDrop: function(e) {
        let eObject = this.eObject(e);
        let data = {...eObject.data};
        // console.log("handleBookingDrop", this.currentlyDragging, e);
            if ($(e.target).hasClass('booking-movable')) {
              this.dataOutput = JSON.stringify({bookingID: data.cursor.id, slotID: data.cursor.slot, totalSlots: data.cursor.total});
              this.bookingDroppedMSG = "This slot is already booked.";
            }
        this.currentlyDragging = null;
        this.loggedEvent = "handleBookingDrop";
      },
      
      handleDrag: function(e) {
        this.loggedEvent = "handleDrag";
        if (!this.currentlyDragging) {
          this.currentlyDragging = e.srcElement;
        }
      },
      
      handleDropBookingIntoCell: function (el) {
          if (this.isEmptySlots(el)) {
            let data = {...el.data}
            console.log(data);
            this.focus.booking = data.cursor.id;
            this.focus.slot = data.dropzone.id;
            $('div.booking.booking-movable[data-id="'+data.cursor.id+'"]').each((index, element) => {
              let dropTD = 'td.week-day-'+data.dropzone.column+(data.dropzone.index + index);
              let dragTD = 'td.week-day-'+data.dragzone.column+(data.dragzone.index + index);
              $(dragTD).removeClass('u-bg-danger').addClass('u-bg-primary');
              $(element).appendTo(dropTD);
              $(dropTD).removeClass('u-bg-primary').addClass('u-bg-danger');
            });
            this.dataOutput = {
              bookingID: data.cursor.id,
              slotID: data.dropzone.id,
              totalSlots: data.cursor.total
            };

            this.updateSlot();

            this.bookingDroppedMSG = "Booking slot has been successfully changed."; 
          }
      },

      /*****************************************************************************************
       *                           Drag and drop methods Ends here
       *****************************************************************************************/

       /*****************************************************************************************
       *                           UPDATE FUNCTIONS START HERE
       *****************************************************************************************/

       updateSlot: function()
       {

          
          axios
            .post('moveSlot/', {

                bookingID : this.dataOutput.bookingID,
                slotID: this.dataOutput.slotID,
                totalSlots: this.dataOutput.totalSlots

            })

            .then(response => {
              this.locations = response.data.data;
            })
            .catch(error => {
              // Hide Loading Toast
              this.loading();

              this.errortoast();
            });

       },

       /*****************************************************************************************
       *                           UPDATE FUNCTIONS END HERE
       *****************************************************************************************/
      
      
      /*****************************************************************************************
       *                          Utility Methods Start Here
       *****************************************************************************************/
        eObject: function (e) {
          let booking = $('#'+this.currentlyDragging.id);
          let source = booking.parents('td');
          let destination = $('#'+e.target.id);
          return {
                e: e,
                el: {booking: booking, source: source, destination: destination},
                target: e.target,
                booking: booking,
                from: source,
                to: destination,
                data: {
                  booking:{},
                  cursor: {
                    id: Number(booking.attr('data-id')),
                    total: Number(booking.attr('data-count')),
                    slotId: Number(booking.attr('data-slot-id')),
                    index: Number(booking.attr('data-booking-index')),
                    column: Number(booking.attr('data-column')),
                  },
                  dragzone: {
                    id: Number(source.attr('data-id')),
                    index: Number(source.attr('data-index')),
                    column: Number(source.attr('data-column')),
                    total: Number(source.attr('data-count'))
                  },
                  dropzone: {
                    id: Number(destination.attr('data-id')),
                    index: Number(destination.attr('data-index')),
                    column: Number(destination.attr('data-column')),
                    availability: true,
                    booked: [],
                  },              
                }
            };
        },
        
        bookingState: function (week) {
          
          return this.focus.slot == week.day.id ? 'u-bg-success' : 'u-bg-primary'
        },
        
        isEmptySlots: function (el) {
          let data = {...el.data};
          let day = $('.day-head-'+data.dropzone.column).attr('data-day');
          let tdAvail = true;
          for (let i = data.dropzone.index; i< (data.dropzone.index + data.cursor.total); i++) {

            if ($('td.week-day-'+data.dropzone.column+i).length > 0) {
                let bookingMovable = $('td.week-day-'+data.dropzone.column+i+' .booking.booking-movable');
                let similarBooking = $('td.week-day-'+data.dropzone.column+i+' .booking.booking-movable[data-id="'+data.cursor.id+'"]');
                if (bookingMovable.length > 0 && similarBooking.length == 0) {
                  if (data.dropzone.availability) data.dropzone.availability = false;
                  data.dropzone.booked.push($('#time-slot-' + i).html());
                }
            } else {
              if (data.dropzone.availability) data.dropzone.availability = false;
              if (tdAvail) tdAvail = false;
            }
            
          }
          if (!data.dropzone.availability) {
            if (!tdAvail) {
              this.bookingDroppedMSG = 'There are missing slots downward. This booking needs ' + data.cursor.total + ' empty slots downward from the drop cell';
            } else {
              this.bookingDroppedMSG = data.dropzone.booked.join(', ') +' slots on ' + day +' are not available. This booking needs ' + data.cursor.total + ' empty slots from the drop cell.';
            }
            this.dataOutput= JSON.stringify({bookingID: data.cursor.id, slotID: data.dropzone.id, totalSlots: data.cursor.total});
          }
          return data.dropzone.availability;
      },
      
      isSlotHasAlreadyBooked: function (el) {
        let exists = false;
        if (el.to[0].tagName === "TD") {
           if (el.to.find('.booking-movable.booking').length > 0) {
             this.bookingDroppedMSG = "This slot is already booked.";
             exists = true;
           }
        } 
        if ($(el.target.srcElement).hasClass('booking-movable')) {
          this.bookingDroppedMSG = "This slot is already booked.";
          exists = true;
        }
        return exists;
      },
      
      focusSlot: function (slot) {
        let booking = $('td.td-dropzone[data-id="'+slot.day.id+'"] .booking');
          this.focus.slot = slot.day.id;
        if (slot.day.booking && booking.length > 0) {
            this.focus.booking = slot.day.booking.id;
          } else {
            if (booking.length > 0) {
              this.focus.booking = Number(booking.attr('data-id'));
            } else {
              this.focus.booking = null;
            }
          }
        },
        
        renderResponse: function (data) {
          // Modify Bookings
          for (let i in data.bookings) {
            let booking = {...data.bookings[i]};
            booking.slots = data.slots.find(slot => booking.id == slot.bookingsId);
            this.table.bookings[i] = booking;
          }
          
          // Modify Slots
          for( let i in data.slots) {
            let slot = {...data.slots[i]};
            if (slot.bookingsId !== null) {
                slot.booking = this.table.bookings.find(booking =>slot.bookingsId == booking.id);
                slot.slotColor = 'u-bg-danger';
            } else {
              slot.slotColor = 'u-bg-primary';
            }
            this.table.slots[i] = slot;
          }
      },
      
      makeWeek: function () {
         // Setting up pairedSlots
        for (let i in this.hours) {
          let daySr = (Number(i) + 1);
          this.pairedSlots.push({
            label: this.hours[i].label,
            week: [
              {index: 1, day: this.table.slots.find(slot => slot.slotType == 'Mon'+daySr && slot.bookingType == this.table.filterBy)},
              {index: 2, day: this.table.slots.find(slot => slot.slotType == 'Tue'+daySr && slot.bookingType == this.table.filterBy)},
              {index: 3, day: this.table.slots.find(slot => slot.slotType == 'Wed'+daySr && slot.bookingType == this.table.filterBy)},
              {index: 4, day: this.table.slots.find(slot => slot.slotType == 'Thu'+daySr && slot.bookingType == this.table.filterBy)},
              {index: 5, day: this.table.slots.find(slot => slot.slotType == 'Fri'+daySr && slot.bookingType == this.table.filterBy)},
            ]
          });
        }
      },
      /*****************************************************************************************
       *                          Utility Methods Start Here
       *****************************************************************************************/
        
      selectSlot(id, index) {
        for (let i in this.bookings) {
          if (!this.bookings[i].areaName) {
            this.bookings[i].slotColor = "u-bg-primary";
          } else if (this.bookings[i].city == this.form.suburb.city) {
            this.bookings[i].slotColor = "u-bg-danger";
          } else {
            this.bookings[i].slotColor = "u-bg-warning";
          }
        }
        this.form.deliverySlot = undefined;
        this.bookings[index].slotColor = "u-bg-success";

        var q;
        for (q = 0; q < this.form.duration.value; q++) {
          if (
            this.bookings[index + q].group == this.bookings[index].group &&
            !this.bookings[index + q].areaName
          ) {
            this.bookings[index + q].slotColor = "u-bg-success";
          }
          if (this.bookings[index + q].areaName) {
            this.bookings[index + q].slotColor = "u-bg-grey-light";
          }
        }

        let indEx = this.deliverySlots.map(item => item.id).indexOf(id);
        this.form.deliverySlot = this.deliverySlots[indEx];
      },

      equipmentChanged() {
        if (
          this.originalSelectedEquipments.length ==
            this.eqForm.selectedEquipments.length &&
          this.form.isChanged != true
        ) {
          this.eqForm.isChanged = false;
        } else {
          this.eqForm.isChanged = true;
        }
      },

      handleSubmit(button = null) {
        this.loading("Saving");

        this.$validator.validateAll(this.form).then(result => {
          if (result) {
            // no errors submit form
            this.submitForm(button);
            return;
          } else {
            // Hide Loading Toast
            this.loading();

            this.errortoast();
          }
        });
      },

      submitForm(button = null) {
        this.form
          .submit("post", this.postUrl)
          .then(success => {
            // set url to add new
            this.$router.push(this.controller + "addnew");

            // clear all and reset to defualt
            this.reset();

            // set url back to original
            this.basePost = this.api + this.controller;
            this.postUrl = this.basePost;
          })
          .catch(error => {
            // Hide Loading Toast
            this.loading();

            this.errortoast();
          });
      },

      goBack() {
        this.resetTrip();

        this.mainDetails = false;
        this.tripEquipmentPanel = true;
      },

      reset() {
        // Display success message
        this.showToast();

        // Reset form using form class
        Object.assign(this.$data, this.$options.data.call(this));

        this.getLocations();

        this.bookingDate = format(new Date(), "DD-MM-YYYY");

        setTimeout(() => {
          this.errors.clear();
        }, 100);
      },

      getLocations() {
        axios
          .get("/area")

          .then(response => {
            this.locations = response.data.data;
          })
          .catch(error => {
            // Hide Loading Toast
            this.loading();

            this.errortoast();
          });
      },

      checkErrors(field) {
        if (this.errors.first(field)) {
          return "danger";
        }
      },

      showToast(message = null) {
        // Hide Loading Toast and Loading from Button
        this.loading();

        // Show success toast
        if (message == null) {
          this.$toast.succeed(
            (this.form.content =
              this.controllerName + " Data Saved Successfully"),
            (this.form.duration = 2000)
          );
        } else {
          this.$toast.succeed(
            (this.form.content = message),
            (this.form.duration = 2000)
          );
        }
      },

      loading(message = null) {
        this.form.isLoading = true;

        const vm = this.toast;

        if (message != null) {
          this.toast = this.$toast.loading(message + "...");
        } else {
          this.form.isLoading = false;
          this.toast.hide();
        }
      },

      errortoast(message, delay) {
        if (message == null) {
          var message = "Please correct errors!";
          var delay = 2000;
        }
        this.$toast.failed(message, delay);
      },

      //New Functions
      setColor(bookings) {
        if (bookings.city == this.form.suburb.city) {
          console.log("hit");
          bookings.slotColor = "u-bg-danger";
        } else if (!bookings.areaName) {
          bookings.slotColor = "u-bg-primary";
        } else if (bookings.areaName) {
          bookings.slotColor = "u-bg-warning";
        }
      },

      canIGoUp(index) {
        var up = index - 1;
        if (
          this.bookings[up] &&
          !this.bookings[up].areaName &&
          this.bookings[up].group == this.bookings[index].group
        ) {
          return true;
        }

        return false;
      },

      canIGoDown(index) {
        var down = index + 1;
        if (
          this.bookings[down] &&
          !this.bookings[down].areaName &&
          this.bookings[down].group == this.bookings[index].group
        ) {
          return true;
        }

        return false;
      },

      takeMeDown(index, id) {
        this.bookings[index + 1].areaName = this.bookings[index].areaName;
        this.bookings[index + 1].city = this.bookings[index].city;
        this.bookings[index + 1].bookingsId = this.bookings[index].bookingsId;

        this.bookings[index].areaName = undefined;
        this.bookings[index].city = undefined;
        this.bookings[index].bookingsId = undefined;
      },

      takeMeUp(index, id) {
        this.bookings[index - 1].areaName = this.bookings[index].areaName;
        this.bookings[index - 1].city = this.bookings[index].city;
        this.bookings[index - 1].bookingsId = this.bookings[index].bookingsId;

        this.bookings[index].areaName = undefined;
        this.bookings[index].city = undefined;
        this.bookings[index].bookingsId = undefined;
      },

      checkDay(day, data) {
        if (format(data.timeSlotEnd, "ddd") == day) {
          return true;
        }
        return false;
      }
    },

    computed: {
      
    },

    watch: {
      
      pairedSlots() {
        let watchPairedSlots = setInterval(() => {
            if ($('td.td-dropzone').length > 0 ) {
              $('td.td-dropzone').each(function (index) {
                let booking = $('#' + this.id + ' .booking');
                if (booking.length > 0) {
                  $(this).addClass('u-bg-danger');
                } else {
                  $(this).removeClass('u-bg-danger');
                }
              });
              clearInterval(watchPairedSlots);
            }
        }, 10);
      },
      upstairs() {
        this.form.upstairs = this.upstairs;
        this.form.chargeForUpstair = undefined;
      },

      duration() {
        this.form.duration = this.duration;
        if (this.duration != undefined) {
            let params = {date: this.bookingDate};
            // this.loading("Fetching...");
            axios.get("/getBookingsByDate", {params: params})
            .then(response => {
              this.renderResponse(response.data);
              
              // Setting up pairedSlots
              this.makeWeek();
              this.table.loaded = true;
            })
            .catch(err => {
              console.log(err);
              // Hide Loading Toast
              this.loading();
              this.errortoast();
            });
        }
        if (this.form.deliverySlot != undefined) {
          this.selectSlot(this.form.deliverySlot.id, this.table.slots.findIndex(slot => slot.id == this.form.deliverySlot.id));
        }
      },
      'table.slots': function (value) {
        // Your code goes here
      },
      


      bookings() {
        var bookings = this.table.slots;
        this.deliverySlots = [];

        for (let field in bookings) {
          bookings[field].date = format(
            bookings[field].timeSlotStart,
            "DD-MM-YYYY"
          );
          bookings[field].group = format(
            bookings[field].timeSlotStart,
            "DDMMYYYY"
          );

          this.setColor(bookings[field]);

          if (this.form.bookingType.label == "One Man") {
            this.deliverySlots.push({
              id: bookings[field].id,
              value: bookings[field].id,
              date: format(bookings[field].timeSlotStart, "DD-MM-YYYY"),
              group: format(bookings[field].timeSlotStart, "DDMMYYYY"),
              label:
                format(bookings[field].timeSlotStart, "DD-MM-YY hh:mm a") +
                " - " +
                bookings[field].bookingType
            });
          } else if (
            this.form.bookingType.label == "Two Man" &&
            this.bookings[field].bookingType == "Two Man"
          ) {
            this.deliverySlots.push({
              id: bookings[field].id,
              value: bookings[field].id,
              date: format(bookings[field].timeSlotStart, "DD-MM-YYYY"),
              group: format(bookings[field].timeSlotStart, "DDMMYYYY"),
              label:
                format(bookings[field].timeSlotStart, "DD-MM-YY hh:mm a") +
                " - " +
                bookings[field].bookingType
            });
          }
        }

        var uniqueArray = [];
        this.deliverySlots.forEach(function(element) {
          if (uniqueArray.map(item => item.group).indexOf(element.group) === -1) {
            uniqueArray.push(element);
          }
        });

        this.dateArray = uniqueArray;
      },

      suburb() {
        this.form.suburb = this.suburb;

        for (let field in this.bookings) {
          this.setColor(this.bookings[field]);
        }
      },
      durationOldFnc() {
        this.form.duration = this.duration;

        // && this.bookings.length == 0
        if (this.duration != undefined) {
          this.loading("Fetching...");

          axios

            .get("/getBookingsByDate", {
              params: {
                date: this.bookingDate
              }
            })

            .then(response => {
              this.bookings = response.data.slots;

              var booked = response.data.bookings;

              var uniqueArray = [];

              for (let i in this.bookings) {
                var indi = booked
                  .map(item => item.id)
                  .indexOf(this.bookings[i].bookingsId);
                var exists = uniqueArray
                  .map(item => item.bookingsId)
                  .indexOf(this.bookings[i].bookingsId);
                if (exists >= 0) {
                  this.bookings[i];
                }
                if (indi >= 0 && exists < 0) {
                  this.bookings[i].areaName = booked[indi].areaName;
                  this.bookings[i].orderNumber = booked[indi].orderNumber;
                  this.bookings[i].customerName = booked[indi].customerName;
                  this.bookings[i].city = booked[indi].cityName;
                  this.bookings[i].slotColor = "u-bg-warning";
                  uniqueArray.push(this.bookings[i]);
                }
              }

              this.loading();
            })
            .catch(error => {
              // Hide Loading Toast
              this.loading();

              this.errortoast();
            });
        }

        if (this.form.deliverySlot != undefined) {
          let selectedIndex = this.bookings
            .map(item => item.id)
            .indexOf(this.form.deliverySlot.id);
          this.selectSlot(this.form.deliverySlot.id, selectedIndex);
        }
      },
    },

    filters: {
      format: function(date) {
        return format(date, "hh:mm a");
      }
    }
  };
</script>
