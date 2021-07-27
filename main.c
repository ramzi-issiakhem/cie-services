#include <stdio.h>
#include <stdlib.h>


FILE *FReservation;
FILE *FLocation;
FILE *FVoiture;
FILE *FClient;

char reservationFileName[];
char locationFileName[];
char voitureFileName[];
char clientFileName[] ;



typedef  struct TVoiture
{
    char matricule[15];
    char marque[50];
    char modele[50];

} TVoiture;

typedef struct TLocation
{
    char matricule[15];
    int numeroClient;
    char date[13];

} TLocation;

typedef struct TClient
{
    int numeroClient;
    char numeroTelephone[15];
    char nom[30];
    char prenom[30];

} TClient;

typedef struct TReservation
{
    char  matricule[15];
    int numeroClient;
    char etat[15];
    char date[13];

} TReservation;

    TVoiture voiture ;
    TClient client;
    TReservation reservation ;
    TLocation location ;


void ajouterReservation();
void ajouterClient();
void ajouterLocation();
void ajouterVoiture();

int removeReservation(int clientNumero);
int removeClient(int clientNumero);
int removeLocation(int clientNumero);
int removeVoiture(char matricule[]);

// Find the First Reservation Matching to the client
TReservation searchReservation(char matricule[]);
// Find the First Location Matching to the client
TLocation searchLocation(char matricule[]);
TClient searchClient(int numeroClient);
TVoiture searchVoiture(char matricule[]);

void secondCase(int i);
void firstCase(int i);

int updateReservations();

void displayCase(int i);
void displayVehiclesState();
void displayReservations();
void displayLocations();
void displayClients();

char reservationFileName[] = "freservation.dat";
char locationFileName[] = "flocation.dat";
char voitureFileName[] = "fvoiture.dat";
char clientFileName[] = "fclient.dat";


int main()
{

    int i;
    int search = 1;
    while (search == 1) {

        printf("What do you want to do ? \n 1- Adding \n 2- Searching  \n 3- Removing \n 4- Updating Reservations \n 5- Display  \n");
        scanf("%d",&i);
        switch (i) {
        case 1:
                    printf("Which File \n 1-Reservation \n 2- Location \n 3- Client \n 4- Vehicles");
                    scanf("%d",&i);

                    switch (i) {
                        case 1: ajouterReservation(); break;
                        case 2: ajouterLocation(); break;
                        case 3: ajouterClient(); break;
                        case 4: ajouterVoiture(); break;
                    }
            break;


            case 2:
                     firstCase(i);
            break;

            case 3:
                     secondCase(i);
                break;
           case 4:
                updateReservations();
            break;

           case 5:
                displayCase(i);
             break;

        }

        printf("Repeat ? 1-0 \n");
        scanf("%d",&search);
    }



}

void displayCase(int i) {
    printf("Which File \n 1-Reservation \n 2- Location \n 3- Client \n 4- Vehicles");
            scanf("%d",&i);

            switch (i) {
                case 1:
                    displayReservations();
                 break;
                case 2:
                    displayLocations();
                break;
                case 3:
                    displayClients();
                break;
                case 4:
                    displayVehiclesState();
                break;
            }

}
void displayClients() {
    FClient = fopen(clientFileName,"rb");

    while(fread(&client,sizeof(struct TClient),1,FClient)) {
        printf("Client Numero : %d  denomme %s %s au mobile %s \n",client.numeroClient , client.nom,client.prenom,client.numeroTelephone );
    }
    printf("------------------------------------ \n\n");
    fclose(FClient);

}
void displayLocations() {
    FLocation = fopen(locationFileName,"rb");

    while(fread(&location,sizeof(struct TLocation),1,FLocation)) {
        printf("Vehicule de matricule %s louée au client numero : %d  le %s \n",location.matricule,location.numeroClient , location.date );
    }
    printf("------------------------------------ \n\n");
    fclose(FLocation);

}
void displayReservations() {
    FReservation = fopen(reservationFileName,"rb");

    while(fread(&reservation,sizeof(struct TReservation),1,FReservation)) {
        printf("Reservation au client numero : %d d'un vehicule de matricule %s pour le %s , etat : %s \n",reservation.numeroClient , reservation.matricule,reservation.date , reservation.etat  );
    }
    printf("------------------------------------ \n\n");
    fclose(FReservation);

}


void firstCase(int i) {
            printf("Which File \n 1-Reservation \n 2- Location \n 3- Client \n 4- Vehicles");
            scanf("%d",&i);
            char matricule[50];
            int numeroClient;

            switch (i) {
                case 1:
                    printf("Inserser le matricule du véhicule \n");
                    scanf("%s",matricule);
                    searchReservation(matricule);
                 break;
                case 2:
                    printf("Inserser le matricule du véhicule \n");
                    scanf("%s",matricule);
                    searchLocation(matricule);
                break;
                case 3:
                    printf("Inserser le numero du client \n");
                    scanf("%d",&numeroClient);
                    searchClient(numeroClient);
                break;
                case 4:
                    printf("Inserser le matricule du véhicule \n");
                    scanf("%s",matricule);
                     searchVoiture(matricule);
                    break;
            }

}
void secondCase(int i) {

            printf("Which File \n 1-Reservation \n 2- Location \n 3- Client \n 4- Vehicles");
            scanf("%d",&i);
            char matricule[20];
            int clientNum;
            switch (i) {
                case 1:
                    printf("Wich client numero woud you want to remove a reservation: \n");
                    scanf("%d",&clientNum);
                    removeReservation(clientNum);
                 break;

                case 2:
                    printf("Wich client numero would you want to remove a location: \n");
                    scanf("%d",&clientNum);
                    removeLocation(clientNum);
                break;

                case 3:
                    printf("Wich client numero  would you use to remove a client : \n");
                    scanf("%d",&clientNum);
                    removeClient(clientNum);
                break;

                case 4:
                    printf("Wich matricule would you want to remove a vehicle: \n");
                    scanf("%s",matricule);
                    removeVoiture(matricule);
                break;
            }

}

void displayVehiclesState() {
    TLocation tempLocation ;
    TReservation tempReservation;

    FVoiture = fopen(voitureFileName,"rb");

    char matricule[20];
    while (fread(&voiture,sizeof(struct TVoiture),1,FVoiture)) {

        strcpy(matricule,voiture.matricule);

        tempLocation.numeroClient = NULL;
        tempReservation.numeroClient = NULL;

        tempLocation    = searchLocation(matricule);
        tempReservation = searchReservation(matricule);

        printf("Vehicule matricule : %s ",voiture.matricule);
        printf(" De Marque %s est ",voiture.modele);

        if ( tempLocation.numeroClient != NULL  ) {
             printf("LOUEE  ");
        } else if (tempReservation.numeroClient != NULL) {
             printf("RESERVE  ");
        } else {
            printf("LIBRE  ");
        }
    }

    printf ("\n ------------------- \n\n");
    fclose(FVoiture);


}

int updateReservations() {
        FILE *temp;
        FReservation = fopen(reservationFileName,"ab+");
        FLocation    = fopen(reservationFileName,"ab+");
        temp         = fopen("temp.dat","ab+");

        if (FReservation == NULL || FLocation == NULL) {
            return 0;
        }

        while(fread(&reservation,sizeof(struct TReservation),1,FReservation)) {

            if ( (strcmp(reservation.etat,"satisfaite") != 0) && (strcmp(reservation.etat,"annulee") != 0) ) {
                    fwrite(&reservation,sizeof(struct TReservation),1,temp);
            }

            if (!strcmp(reservation.etat,"satisfaite")) {

                    printf("found one HERE !! ");
                    strcpy(reservation.date,location.date);
                    strcpy(reservation.matricule,location.matricule);
                    location.numeroClient = reservation.numeroClient;

                    fwrite(&location,sizeof(struct TLocation),1,FLocation);
            }


        }

            fclose(FReservation);
            fclose(temp);
            fclose(FLocation);
            remove(reservationFileName);
            rename("temp.dat",reservationFileName);

        return 1;



}

TReservation searchReservation(char matricule[]) {

        FReservation = fopen(reservationFileName,"rb");

        while(fread(&reservation,sizeof(struct TReservation),1,FReservation)) {
            if (strcmp(reservation.matricule,matricule) == 0) {
                printf("Reservation Found \n");
                return reservation;
            }
        }

        fclose(FReservation);
}

TClient searchClient(int numeroClient) {

        FClient = fopen(clientFileName,"rb");

        while( fread(&client,sizeof(struct TClient),1,FClient) ) {
            if (client.numeroClient == numeroClient) {
                printf("Client Found \n");
                return client;
            }
        }
        fclose(FClient);
}


TLocation searchLocation(char matricule[]) {

        FLocation = fopen(locationFileName,"rb");

        while(fread(&location,sizeof(struct TLocation),1,FLocation)) {
            if (strcmp(location.matricule,matricule) == 0) {
                printf("Location Found \n");
                return location;
            }
        }

        fclose(FLocation);
}


TVoiture searchVoiture(char matricule[]) {

        FVoiture = fopen(voitureFileName,"rb");

        while(fread(&voiture,sizeof(struct TVoiture),1,FVoiture)) {
            if ( strcmp(voiture.matricule,matricule) == 0) {
                printf("Vehicle Found \n");
                return voiture;
            }
        }

        fclose(FVoiture);
}



int removeClient(int clientNumero) {
    FILE *temp;
    int returnType = 0;
    FClient = fopen(clientFileName,"ab+");
    temp = fopen("temp.dat","wb");

    while (fread(&client,sizeof(struct TClient),1,FClient)) {

        if (client.numeroClient != clientNumero) {
            fwrite(&client,sizeof(struct TClient),1,temp);
        } else {
            removeLocation(clientNumero);
            removeReservation(clientNumero);
            printf("Client deleted \n");
            returnType = 1;
        }
    }

        fclose(FClient);
        fclose(temp);
        remove(clientFileName);
        rename("temp.dat",clientFileName);


        return returnType;
}

int removeVoiture(char matricule[]) {
    FILE *temp;
    int returnType = 0;
    FVoiture = fopen(voitureFileName,"ab+");
    temp = fopen("temp.dat","wb");

    while (fread(&voiture,sizeof(struct TVoiture),1,FVoiture)) {

        if (strcmp(voiture.matricule,matricule) != 0 ) {
            fwrite(&voiture,sizeof(struct TVoiture),1,temp);
        } else {
            printf("Vehicle Deleted \n");
            returnType = 1;
        }
    }

        fclose(FVoiture);
        fclose(temp);
        remove(voitureFileName);
        rename("temp.dat",voitureFileName);


        return returnType;
}

int removeLocation (int clientNumero) {
    FILE *temp;
    int returnType = 0;
    FLocation = fopen(locationFileName,"ab+");
    temp = fopen("temp.dat","wb");

    while (fread(&location,sizeof(struct TLocation),1,FLocation)) {

        if (location.numeroClient != clientNumero) {
            fwrite(&location,sizeof(struct TLocation),1,temp);
        } else {
            printf("Location deleted \n");
            returnType = 1;
        }
    }

        fclose(FLocation);
        fclose(temp);
        remove(locationFileName);
        rename("temp.dat",locationFileName);

        return returnType;
}

int removeReservation(int clientNumero) {
    FILE *temp;
    int returnType = 0;
    FReservation = fopen(reservationFileName,"ab+");
    temp = fopen("temp.dat","wb");

    while (fread(&reservation,sizeof(struct TReservation),1,FReservation)) {

        if (reservation.numeroClient == clientNumero) {
            printf("Reservation deleted\n");
            returnType = 1;
        } else {
            fwrite(&reservation,sizeof(struct TReservation),1,temp);
        }
    }

        fclose(FReservation);
        fclose(temp);
        remove(reservationFileName);
        rename("temp.dat",reservationFileName);


        return returnType;
}


void ajouterVoiture()
{


        printf("Adding a vehicle : \n");

        printf("Matricule : ");
        scanf(" %100[^\n]", voiture.matricule);

        printf("\n Marque : ");
        scanf(" %100[^\n]",voiture.marque);

        printf("\n Modele : ");
        scanf(" %100[^\n]", voiture.modele);

        FVoiture = fopen(voitureFileName,"ab");
        if (FVoiture != NULL)
        {
            fwrite(&voiture, sizeof(struct TVoiture), 1, FVoiture);
        }
        fclose(FVoiture);

}

 void ajouterClient() {

        printf("Adding a client : \n");

        printf("Numero Client : ");
        scanf(" %d", &client.numeroClient);

        printf("\n Nom : ");
        scanf(" %100[^\n]", client.nom);

        printf("\n Prenom : ");
        scanf(" %100[^\n]", client.prenom);

        printf("\n Numéro de telephone : ");
        scanf(" %100[^\n]", client.numeroTelephone);

        FClient = fopen(clientFileName,"ab");
        if (FClient != NULL)
        {
            fwrite(&client, sizeof(struct TClient), 1, FClient);
        }
        fclose(FClient);
 }

 void ajouterReservation() {

        printf("Adding a Reservation : \n");

        printf("Matricule : ");
        scanf(" %100[^\n]", reservation.matricule);

        printf("\n Date : ");
        scanf(" %100[^\n]", reservation.date);

        printf("\n Numero Client : ");
        scanf(" %i", &reservation.numeroClient);

        printf("\n Etat Satisfaite-En Attente-Annulee : ");
        scanf(" %100[^\n]", reservation.etat);

        FReservation = fopen(reservationFileName,"ab");
        if (FReservation != NULL)
        {
            fwrite(&reservation, sizeof(struct TReservation), 1, FReservation);
        }
        fclose(FReservation);
 }

 void ajouterLocation() {

        printf("Adding a Location : \n");

        printf("Matricule : ");
        scanf(" %100[^\n]", location.matricule);

        printf("\n Numéro Client : ");
        scanf(" %d", &location.numeroClient);

        printf("\n Date : ");
        scanf(" %100[^\n]", location.date);


        FLocation = fopen(locationFileName,"ab");
        if (FLocation != NULL)
        {
            fwrite(&location, sizeof(struct TLocation), 1, FLocation);
        }
        fclose(FLocation);
}





