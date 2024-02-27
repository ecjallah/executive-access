/*!
 * Purpose: contains shared components for all modules. 
 * Version Release: 1.0
 * Created Date: July 6, 2023
 * Author(s):Enoch C. Jallah
 * Contact Mail: enochcjallah@gmail.com
*/
import { PageLessModal } from "../../PageLess/Modal.js";
import "../Components.js";

export class Modal extends PageLessModal{
    
    constructor(name = null){
        super(name);
    }
}