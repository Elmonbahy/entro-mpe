import "./bootstrap";
import TomSelect from "tom-select";
import mask from "@alpinejs/mask";
import flatpickr from "flatpickr";
import {
  Livewire,
  Alpine,
} from "../../vendor/livewire/livewire/dist/livewire.esm";

// Powergrid assets
import "./../../vendor/power-components/livewire-powergrid/dist/powergrid";
import "./../../vendor/power-components/livewire-powergrid/dist/bootstrap5.css";

window.TomSelect = TomSelect;
window.flatpickr = flatpickr;

Alpine.plugin(mask);
Livewire.start();
