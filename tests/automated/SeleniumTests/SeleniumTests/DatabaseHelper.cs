using MySql.Data.MySqlClient;
using System;
using System.Collections.Generic;
using System.Data;
using System.Text;

namespace SeleniumTests {
    static class DatabaseHelper {

        static string dbConnectionString = "server=localhost;userid=root;database=kpmessengerdb";
        static MySqlConnection mysqlconnection;

        public static void RemoveFriendConnection(int user1ID, int user2ID) {

            string removeFriendRequest1 = "DELETE FROM friend WHERE SenderID = '" + user1ID + "' AND RecipientID = '" + user2ID + "';";
            CallQuery(removeFriendRequest1);

            string removeFriendRequest2 = "DELETE FROM friend WHERE SenderID = '" + user2ID + "' AND RecipientID = '" + user1ID + "';";
            CallQuery(removeFriendRequest2);
        }

        public static void  RemoveUser(string userName) {
            //not doing this this way anymore, it should be done through the gui
            string removeUser = "DELETE FROM _user WHERE _user.UserName = '" + userName + "';";
            CallQuery(removeUser);

        }

        static void CallQuery(string stringQuery) {

            if (stringQuery != "") {

                try {

                    using (mysqlconnection = new MySqlConnection(dbConnectionString)) {

                        mysqlconnection.Open();

                        using (MySqlCommand dbQuery = mysqlconnection.CreateCommand()) {

                            dbQuery.CommandType = CommandType.Text;
                            dbQuery.CommandTimeout = 300;
                            dbQuery.CommandText = stringQuery;

                            int rowsAffected = dbQuery.ExecuteNonQuery();

                            mysqlconnection.Close();
                        }
                    }
                } catch (MySqlException ex) {

                    Console.WriteLine("Couldn't open or query the database. Error: " + ex.Message);
                }
            }
        }
    }
}
