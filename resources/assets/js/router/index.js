
import Login from "./../components/login.vue";
import Register from "./../components/register.vue";
import Home from "./../components/home.vue";

import ClientsAll from "./../components/clients/view_all.vue";
import ClientsAddNew from "./../components/clients/form.vue";
import ClientsEdit from "./../components/clients/form.vue";

import StaffAll from "./../components/staff/view_all.vue";
import StaffAddNew from "./../components/staff/form.vue";
import StaffEdit from "./../components/staff/form.vue";

import StaffCertificationsAll from "./../components/staffcertifications/view_all.vue";
import StaffCertificationsAddNew from "./../components/staffcertifications/form.vue";
import StaffCertificationsEdit from "./../components/staffcertifications/form.vue";

import CityAll from "./../components/city/view_all.vue";
import CityAddNew from "./../components/city/form.vue";
import CityEdit from "./../components/city/form.vue";

import AreaAll from "./../components/area/view_all.vue";
import AreaAddNew from "./../components/area/form.vue";
import AreaEdit from "./../components/area/form.vue";

import SupplierAll from "./../components/supplier/view_all.vue";
import SupplierAddNew from "./../components/supplier/form.vue";
import SupplierEdit from "./../components/supplier/form.vue";

import VariationAll from "./../components/variation/view_all.vue";
import VariationAddNew from "./../components/variation/form.vue";
import VariationEdit from "./../components/variation/form.vue";

import EquipTypeAll from "./../components/equipmenttype/view_all.vue";
import EquipTypeAddNew from "./../components/equipmenttype/form.vue";
import EquipTypeEdit from "./../components/equipmenttype/form.vue";

import EquipmentAll from "./../components/equipments/view_all.vue";
import EquipmentAddNew from "./../components/equipments/form.vue";
import EquipmentEdit from "./../components/equipments/form.vue";

import ExpensetypeAll from "./../components/expensetype/view_all.vue";
import ExpensetypeAddNew from "./../components/expensetype/form.vue";
import ExpensetypeEdit from "./../components/expensetype/form.vue";
import ExpenseWaitingApproval from "./../components/expense/waitingapproval.vue";
import ExpenseFlagged from "./../components/expense/flagapproval.vue";
import ExpenseApproved from "./../components/expense/approved.vue";
import ExpenseDeleted from "./../components/expense/deleted.vue";
import ExpenseAddNew from "./../components/expense/addExpense.vue";
import ExpenseAddFuel from "./../components/expense/addFuel.vue";

import FuelReport from "./../components/bills/fuelReport.vue";
import FuelReportGrouped from "./../components/bills/fuelReport_bill_grouped.vue";
import FuelReportDummy from "./../components/bills/fuelReportDummy.vue";
import CreateBill from "./../components/bills/form.vue";
import ViewBills from "./../components/bills/view_all_bills.vue";
import BillFuelWaitingApproval from "./../components/bills/waitingapproval_bill.vue";

import AdvanceSlip from "./../components/expense/advanceslip.vue";


import DispatchAll from "./../components/dispatch/view_all.vue";
import DispatchAddNew from "./../components/dispatch/form.vue";
import DispatchEdit from "./../components/dispatch/form.vue";
import LoadTrips from "./../components/dispatch/loadTrips.vue";

import TripsManagementAll from "./../components/trips/view_all.vue";
import TripLog from "./../components/trips/log.vue";
import TripDeleted from "./../components/trips/deleted.vue";
import TripsManagementAddNew from "./../components/trips/form.vue";
import TripsManagementEdit from "./../components/trips/loadTrips.vue";
import TripsManagementExpense from "./../components/trips/loadExpense.vue";
import TripsManagementFuel from "./../components/trips/loadFuel.vue";
import CreateVoucher from "./../components/trips/createVoucher.vue";
import TMReplaceEquipment from "./../components/trips/replaceEquipment.vue";
import DailyReport from "./../components/dispatchReports/dailyreport.vue";
import bulkupdate from "./../components/dispatchReports/bulkupdate.vue";

import ExpenseSupplierAll from "./../components/expensesupplier/view_all.vue";
import ExpenseSupplierAddNew from "./../components/expensesupplier/form.vue";
import ExpenseSupplierEdit from "./../components/expensesupplier/form.vue";

import UsersAll from "./../components/users/view_all.vue";
import UsersAddNew from "./../components/users/form.vue";
import UsersEdit from "./../components/users/form.vue";


const router = [

  { path: '/', component: Home, name: 'home' },
  { path: '/login', component: Login , name: 'auth.login' },
  { path: '/register', component: Register , name: 'register' },

  { path: '/getusers/all', component: UsersAll , name: 'getusers/all', meta: { auth: true }  },
  { path: '/getusers/:id/edit', component: UsersEdit , name: 'getusers/:id/edit', meta: { auth: true }  },
  { path: '/getusers/addnew', component: UsersAddNew , name: 'getusers/addnew', meta: { auth: true }  },
  
  { path: '/clients/all', component: ClientsAll , name: 'clients/all', meta: { auth: true }  },
  { path: '/clients/:id/edit', component: ClientsEdit , name: 'clients/:id/edit', meta: { auth: true }  },
  { path: '/clients/addnew', component: ClientsAddNew , name: 'clients/addnew', meta: { auth: true }  },
  
  { path: '/staff/all', component: StaffAll , name: 'staff/all', meta: { auth: true }  },
  { path: '/staff/:id/edit', component: StaffEdit , name: 'staff/:id/edit', meta: { auth: true }  },
  { path: '/staff/addnew', component: StaffAddNew , name: 'staff/addnew', meta: { auth: true }  },

  { path: '/certificate/all', component: StaffCertificationsAll , name: 'certificate/all', meta: { auth: true }  },
  { path: '/certificate/:id/edit', component: StaffCertificationsEdit , name: 'certificate/:id/edit', meta: { auth: true }  },
  { path: '/certificate/addnew', component: StaffCertificationsAddNew , name: 'certificate/addnew', meta: { auth: true }  },

  { path: '/city/all', component: CityAll , name: 'city/all', meta: { auth: true }  },
  { path: '/city/addnew', component: CityAddNew , name: 'city/addnew', meta: { auth: true }  },
  { path: '/city/:id/edit', component: CityEdit , name: 'city/:id/edit', meta: { auth: true }  },

  { path: '/area/all', component: AreaAll , name: 'area/all', meta: { auth: true }  },
  { path: '/area/addnew', component: AreaAddNew , name: 'area/addnew', meta: { auth: true }  },
  { path: '/area/:id/edit', component: AreaEdit , name: 'area/:id/edit', meta: { auth: true }  },

  { path: '/supplier/all', component: SupplierAll , name: 'supplier/all', meta: { auth: true }  },
  { path: '/supplier/addnew', component: SupplierAddNew , name: 'supplier/addnew', meta: { auth: true }  },
  { path: '/supplier/:id/edit', component: SupplierEdit , name: 'supplier/:id/edit', meta: { auth: true }  },

  { path: '/variation/all', component: VariationAll , name: 'variation/all', meta: { auth: true }  },
  { path: '/variation/addnew', component: VariationAddNew , name: 'variation/addnew', meta: { auth: true }  },
  { path: '/variation/:id/edit', component: VariationEdit , name: 'variation/:id/edit', meta: { auth: true }  },

  { path: '/equipmenttype/all', component: EquipTypeAll , name: 'equipmenttype/all', meta: { auth: true }  },
  { path: '/equipmenttype/addnew', component: EquipTypeAddNew , name: 'equipmenttype/addnew', meta: { auth: true }  },
  { path: '/equipmenttype/:id/edit', component: EquipTypeEdit , name: 'equipmenttype/:id/edit', meta: { auth: true }  },

  { path: '/equipment/all', component: EquipmentAll , name: 'equipment/all', meta: { auth: true }  },
  { path: '/equipment/addnew', component: EquipmentAddNew , name: 'equipment/addnew', meta: { auth: true }  },
  { path: '/equipment/:id/edit', component: EquipmentEdit , name: 'equipment/:id/edit', meta: { auth: true }  },

  { path: '/expensetype/all', component: ExpensetypeAll , name: 'expensetype/all', meta: { auth: true }  },
  { path: '/expensetype/addnew', component: ExpensetypeAddNew , name: 'expensetype/addnew', meta: { auth: true }  },
  { path: '/expensetype/:id/edit', component: ExpensetypeEdit , name: 'expensetype/:id/edit', meta: { auth: true }  },

  { path: '/expense/addExpense', component: ExpenseAddNew , name: 'expense/addExpense', meta: { auth: true }  },
  { path: '/expense/addFuel', component: ExpenseAddFuel , name: 'expense/addFuel', meta: { auth: true }  },
  { path: '/expense/waitingapproval', component: ExpenseWaitingApproval , name: 'expense/waitingapproval', meta: { auth: true }  },
  { path: '/expense/flagged', component: ExpenseFlagged , name: 'expense/flagged', meta: { auth: true }  },
  { path: '/expense/approved', component: ExpenseApproved , name: 'expense/approved', meta: { auth: true }  },
  { path: '/expense/deleted', component: ExpenseDeleted , name: 'expense/deleted', meta: { auth: true }  },

  { path: '/bill/createbill', component: CreateBill , name: 'bill/createbill', meta: { auth: true }  },
  { path: '/bill/:id/edit', component: CreateBill, name: 'bill/:id/edit', meta: { auth: true }  },
  { path: '/expense/addFuel/:supplier_id/:bill_id', component: ExpenseAddFuel , name: 'expense/addFuel/:supplier_id/:bill_id', meta: { auth: true }  },
  { path: '/expense/waitingapproval/:supplier_id/:bill_id', component: BillFuelWaitingApproval , name: 'expense/waitingapproval/:supplier_id/:bill_id', meta: { auth: true }  },
  { path: '/bills/:status/view', component: ViewBills , name: 'bills/:status/view', meta: { auth: true }  },

  { path: '/expense/advanceslip', component: AdvanceSlip , name: 'expense/advanceslip', meta: { auth: true }  },

  { path: '/expensesupplier/all', component: ExpenseSupplierAll , name: 'expensesupplier/all', meta: { auth: true }  },
  { path: '/expensesupplier/addnew', component: ExpenseSupplierAddNew , name: 'expensesupplier/addnew', meta: { auth: true }  },
  { path: '/expensesupplier/:id/edit', component: ExpenseSupplierEdit , name: 'expensesupplier/:id/edit', meta: { auth: true }  },
  

  { path: '/booking/all', component: DispatchAll , name: 'booking/all' , meta: { auth: true } },
  { path: '/booking/addnew', component: DispatchAddNew , name: 'booking/addnew', meta: { auth: true }  },
  { path: '/booking/:id/edit', component: DispatchEdit , name: 'booking/:id/edit', meta: { auth: true }  },
  { path: '/booking/loadtrips/:id/:equipment_id', component: LoadTrips , name: 'booking/loadTrips/:id/:equipment_id', meta: { auth: true }  },
  { path: '/booking/dailyreport', component: DailyReport , name: 'booking/dailyreport', meta: { auth: true }  },
  { path: '/booking/bulkupdate', component: bulkupdate , name: 'booking/bulkupdate', meta: { auth: true }  },

  { path: '/tripmanagement/all', component: TripsManagementAll , name: 'tripmanagement/all', meta: { auth: true }  },
  { path: '/tripmanagement/addnew', component: TripsManagementAddNew , name: 'tripmanagement/addnew', meta: { auth: true }  },
  { path: '/tripmanagement/:trip_id/log', component: TripLog , name: 'tripmanagement/:trip_id/log', meta: { auth: true }  },
  { path: '/tripmanagement/:dispatch_id/:equipment_id/deleted', component: TripDeleted , name: 'tripmanagement/:dispatch_id/:equipment_id/deleted', meta: { auth: true }  },
  { path: '/dispatchexpense/:dispatch_id/:equipment_id/:status/:equipmentNumber', component: TripsManagementExpense , name: 'dispatchexpense/:dispatch_id/:equipment_id/:status/:equipmentNumber', meta: { auth: true }  },
  { path: '/dispatchfuel/:dispatch_id/:equipment_id/:status/:equipmentNumber', component: TripsManagementFuel , name: 'dispatchfuel/:dispatch_id/:equipment_id/:status/:equipmentNumber', meta: { auth: true }  },
  { path: '/tripmanagement/:id/:equipment_id', component: TripsManagementEdit , name: 'tripmanagement/:id/:equipment_id', meta: { auth: true }  },
  { path: '/createVoucher/:dispatch_ids/:equipment_id', component: CreateVoucher , name: 'createVoucher/:dispatch_ids/:equipment_id', meta: { auth: true }  },
  { path: '/replace_equipment/:id/:equipment_id', component: TMReplaceEquipment , name: 'replace_equipment/:id/:equipment_id', meta: { auth: true }  },

  //Report
  { path: '/reports/fuelSupplierReport', component: FuelReport , name: 'reports/fuelSupplierReport', meta: { auth: true }  },
  { path: '/reports/fuelGrouped', component: FuelReportGrouped , name: 'reports/fuelGrouped', meta: { auth: true }  },
  { path: '/reports/fuelSupplierReport_test', component: FuelReportDummy , name: 'reports/fuelSupplierReport_test', meta: { auth: true }  },

]

export default router;