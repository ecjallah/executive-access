/*!
 * Purpose: contains shared components for all modules. 
 * Version Release: 1.0
 * Created Date: July 6, 2023
 * Author(s):Enoch C. Jallah
 * Contact Mail: enochcjallah@gmail.com
*/
// "use esversion: 11";
import { PageLessComponent } from '../../PageLess/PageLess.min.js';
import { PageLess } from '../../PageLess/PageLess.min.js';
import { PageLessModal } from "../../PageLess/Modal.js";
import "../Components.js";

export class Modal extends PageLessModal{
    
    constructor(name = null){
        super(name);
    }
}