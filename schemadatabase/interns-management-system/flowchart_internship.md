flowchart TD
%% Global Styling Definitions
classDef actor fill:#fef3c7,stroke:#d97706,stroke-width:2px,color:#000;
classDef module fill:#e0f2fe,stroke:#0284c7,stroke-width:2px,color:#000;
classDef data fill:#fee2e2,stroke:#dc2626,stroke-width:2px,color:#000;
classDef auto fill:#dcfce7,stroke:#16a34a,stroke-width:2px,color:#000;

subgraph Onboarding ["1. Onboarding & Profiling (Database Setup)"]
direction TB
A_Admin([HC/Admin])
A_Admin -->|Create Intern Profile| A1[Upload Contracts & Select Division]
A1 -->|Assign Responsible Staff| A2[(Link Intern to Mentor ID)]
end

subgraph Monitoring ["3. Monitoring & Attendance (Daily Routine)"]
direction TB
I_Intern([Intern])
I_Intern -->|Start Day| M1[Trigger Geotagging API]
M1 -->|Coordinates Validated| M2[(Save Attendance Log)]

I_Intern -->|End of Day| M3[Fill out Daily Activity]
M3 -->|Submit| M4[Wait for Mentor Verification]
end

subgraph TaskMgmt ["2. Task Assignment & Performance (Kanban)"]
direction TB
M_Mentor([Mentor])
M_Mentor -->|Create Assignment| T1[Add to To-Do Board]

T1 -->|Task Assigned| I_Intern
I_Intern -.->|Drag to In Progress| T2[Working on Task]
T2 -.->|Drag to Done| T3[Task Completed]

T3 -->|Review Work| M_Mentor
M4 -->|Check Activity| M_Mentor

M_Mentor -->|Rates 1-5| T4[(Save Task Score)]
M_Mentor -->|Clicks Verify| T5[(Validate Logbook)]
end

subgraph Evaluation ["4. Evaluation & Offboarding (Program End)"]
direction TB
T4 -.-> E1[Trigger Final Evaluation]
T5 -.-> E1

M_Mentor -->|Fills Rubric| E2[Hard/Soft Skills Grades]
I_Intern -->|Fills Survey| E3[Program Feedback]

E2 --> E4{System Aggregates Final Data}
E3 --> E4

E4 -->|Passed Requirements| E5[Auto-Generate PDF Certificate]
end

%% Cross-module relationships
A2 ==> |Account Activated| I_Intern
A2 ==> |Notifies| M_Mentor

%% Class assignments (SAFE way)
class A_Admin,I_Intern,M_Mentor actor;
class A1,M1,M3,M4,T1,T2,T3,E1,E2,E3 module;
class A2,M2,T4,T5 data;
class E4,E5 auto;