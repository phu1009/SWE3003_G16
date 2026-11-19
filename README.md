# Long Chau Pharmacy Management System (LC-PMS) – SWE30003 Group 16

Group project for **SWE30003 Software Architectures and Design** at Swinburne University of Technology (Hanoi), June – August 2025. The team analysed the Long Chau pharmacy-chain case study, produced an object-oriented design, and implemented part of the design as a PHP/MySQL web application.

## Design documents
| Report | Content |
| --- | --- |
| [A1 – Requirements specification](docs/A1_Requirements_Specification_Group16.pdf) | Domain analysis, key actors, data model, workflows, 8 functional tasks and quality attributes |
| [A2 – Object design](docs/A2_Object_Design_Group16.pdf) | Candidate classes, UML class diagram, CRC cards, design heuristics and patterns (Factory Method, Singleton, Strategy, Observer, MVC) |
| [A3 – Design implementation](docs/A3_Design_Implementation_Group16.pdf) | Updated design (classes, UML, CRC cards), reflection on the A2 design, implemented features, platforms and deployment |

## Implementation
- **Authentication & roles** – login / register, role-based access (`lib/Auth.php`, `public/api/auth/roles.php`)
- **Product catalogue** – browse, add, edit and delete products with image upload (`public/catalogue.php`, `public/api/modules/product/*`, `lib/FileUpload.php`)
- **User profile** – profile details and multiple delivery addresses (`public/profile.php`, `public/api/modules/user/*`)
- **Database** – normalised MySQL schema for users, roles, branches, products, inventory, prescriptions, orders, payments and notifications (`database/schema.sql`, `database/seed.sql`)
- Model classes in `app/Models`, PDO wrapper in `lib/Database.php`

Tech stack: PHP · MySQL · HTML/CSS · JavaScript (fetch API)

## Run
1. Import `database/schema.sql` then `database/seed.sql` into MySQL.
2. Set your database details in `config/config.php`.
3. Serve the `public/` folder, e.g. `php -S localhost:8000 -t public`.

## Team workflow

Pull or push changes to Repo: 

1. Clone repo:	git clone <https://github.com/phu1009/SWE3003_G16>
2. Pull updates:	git pull origin main
3. Push updates: 
    3.1. Add changes:	    git add .
    3.2. Commit changes:	git commit -m "message"
    3.3. Push changes:	    git push origin main
