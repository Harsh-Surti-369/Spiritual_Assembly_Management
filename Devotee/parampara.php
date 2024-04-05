<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Spiritual Leaders</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        /* CSS for showing and hiding leader details */
        .leader-details {
            display: none;
            margin-top: 20px;
        }

        .leader-details.active {
            display: block;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }

        .leader-images {
            display: flex;
            justify-content: space-around;
            align-items: flex-start;
            /* Align items to the top */
            flex-wrap: wrap;
            /* Allow images to wrap */
            margin-bottom: 20px;
        }

        .leader-images img {
            width: 150px;
            height: auto;
            cursor: pointer;
            transition: transform 0.3s ease;
            margin-bottom: 10px;
        }

        .leader-images img:hover {
            transform: scale(1.1);
        }

        .leader-details {
            width: 100%;
            /* Take full width */
        }

        .leader-details h2 {
            font-size: 24px;
            margin-bottom: 10px;
        }

        .leader-details p {
            font-size: 18px;
            line-height: 1.6;
            margin-bottom: 10px;
        }
    </style>
</head>

<body>

    <div class="container">
        <h1> Spiritual Leaders</h1>
        <div id="leader-details-container">
            <div class="leader-images">
                <img src="/01-Bhagwaan-Swaminarayan.png" alt="Leader 1" onclick="toggleLeaderDetails(1)">
                <img src="/02-Gunatitanand-Swami.png" alt="Leader 2" onclick="toggleLeaderDetails(2)">
                <img src="/03-Shastriji-Maharaj.png" alt="Leader 3" onclick="toggleLeaderDetails(3)">
                <img src="/04-Yogiji-Maharaj.png" alt="Leader 4" onclick="toggleLeaderDetails(4)">
                <img src="/05-Hariprasad-Swamiji-Maharaj.png" alt="Leader 5" onclick="toggleLeaderDetails(5)">
            </div>
            <div id="leader-details-2" class="leader-details">
                <h2>Swami jee Maharaj</h2>
                <p>Details about Leader 1</p>
                <p class="font-size-18">
                    Appearance: 1781 A.D
                </p>

                <p class="font-size-18">Name: Ghanshyam Maharaj. (Known as Nilkanth Varni in Van-Vicharan)</p>

                <p class="font-size-18">Name After Initiation: Swami Sahajanand</p>

                <p class="font-size-18">Place of Birth: Chhappaiya (Dist. Ayodhya, Uttar Pradesh, India)</p>

                <p class="font-size-18">Life Work:Initiated Swaminarayan Sect Propagated Vishishtadwaita (Dualism) at the age of seven Initiated 500Paramhansas at the age of 21 Put an end to violent oblation, traditions of sati (to sit on the pyre of the dead husband and burn oneself), child marriage, infanticide Gave first prose in binded form in Gujarati language Special worshipping areas for women Promulgation of Nishkaam Dharma and Nirman Dharma Absolute and total celibacy for saints after initiation</p>

                <p class="font-size-18">Departed: 1830 A.D.</p>

                <p class="font-size-18">Bhagwaan Shree Swaminarayan manifested Himself in the year 1781 A.D. 224 years ago in the small hamlet of Chhapaiya near Ayodhya in Northern India. As the proverb goes, 'The qualities of son are known from His birth.' Similarly, Ghanshyam showed a lot of dexterity in deeds and divinity in action during His tender age. As Bhagavatacharya Sri Markendey prophesied, 'this kid is the manifestation of God. He shall bring absolute happiness and prosperity to one and all whoever will come into His divine contact.' True to the words, He won the scriptural debate among the pandits of India in Kashi at the age of seven years.</p>

                <p class="font-size-18">He renounced the worldly life at the age of eleven and was known as Nilkanth Varni. During His seven and half years of sojourn in the jungle, He walked the whole span of greater India from greater Himalayas to the end of land, Kanyakumari in South and Assam in East and Gujarat in west. At the end of His entire journey which set the path for the benefit for all and triumph over all evil, He settled in the village of Loj in Saurashtra. He accepted Ramanand Swamiji as a Guru for all humanity:</p>

                <p class="font-size-18">Let me suffer million times but do not let my devotee suffer one bit.My devotee shall never go hungry even if he is the victim of seven famines.I shall take my devotee to the abode of God - Akshardham at the end of His mortal life. I shall be present on this Earth eternally through the God-Ordained Saint who has renounced women, wealth and fame.</p>

                <p class="font-size-18">He initiated 500 Paramhansas (saints) in one night at the age of 21. He was the mentor of millions of persons who had never visualized the happiness that was to be their domain. Most of them were uneducated warriors and farmers. He conquered sword wielding warriors not by wielding sword but immersing them in His divine love.</p>

                <p class="font-size-18">He resurrected them from the brink of barbarism and elevated them to the level of God fearing devotees. He was probably the most advanced reformer who worked at the lowest strata of the society enriching them to be the part of the happy, serene and truthful society. He put an end to practice of Sati, infanticide and many other vices that plagued the society of yore. Joban Pagi, Bhaguji, Alaiya Khachar etc. are the examples of His unflinching effort to revive humanity to it's' best. He wrote 'Shikshapatri', 212 verses of perennial importance that if followed, would bring ethereal peace and prosperity; in the material world and metaphysical world. He also dictated discourses known as 'Vachanamritam', the first compiled prose in the history of Gujarati Language.</p>

                <p class="font-size-18">He left His mortal body at the age of 49 yrs., living an indelible impression on the future. Millions of lives were transformed from human to superlative being through His divine contact.</p>

                <p class="font-size-18">He always said, 'The objective of My manifestation in this world is to cultivate Service and Devotion.' Service unto Humanity and Devotion unto God are the two goals which every follower of Bhagwaan Shree Swaminarayan followed. In a nutshell, He was the propagator of Nishkam-Dharma, the spirituality of desirelessness.</p>
            </div>
            <!-- <div id="leader-details-2" class="leader-details">
            <h2>Leader 2</h2>
            <p>Details about Leader 2</p> -->
            <!-- </div> -->
            <!-- Add more leader details divs as needed -->
            <div id="leader-details-3" class="leader-details">
                <h2>Gunatitanand Swami jee</h2>
                <p>Details about Leader 1</p>
                <p class="font-size-18">
                    Appearance: 1781 A.D
                </p>

                <p class="font-size-18">Name: Ghanshyam Maharaj. (Known as Nilkanth Varni in Van-Vicharan)</p>

                <p class="font-size-18">Name After Initiation: Swami Sahajanand</p>

                <p class="font-size-18">Place of Birth: Chhappaiya (Dist. Ayodhya, Uttar Pradesh, India)</p>

                <p class="font-size-18">Life Work:Initiated Swaminarayan Sect Propagated Vishishtadwaita (Dualism) at the age of seven Initiated 500Paramhansas at the age of 21 Put an end to violent oblation, traditions of sati (to sit on the pyre of the dead husband and burn oneself), child marriage, infanticide Gave first prose in binded form in Gujarati language Special worshipping areas for women Promulgation of Nishkaam Dharma and Nirman Dharma Absolute and total celibacy for saints after initiation</p>

                <p class="font-size-18">Departed: 1830 A.D.</p>

                <p class="font-size-18">Bhagwaan Shree Swaminarayan manifested Himself in the year 1781 A.D. 224 years ago in the small hamlet of Chhapaiya near Ayodhya in Northern India. As the proverb goes, 'The qualities of son are known from His birth.' Similarly, Ghanshyam showed a lot of dexterity in deeds and divinity in action during His tender age. As Bhagavatacharya Sri Markendey prophesied, 'this kid is the manifestation of God. He shall bring absolute happiness and prosperity to one and all whoever will come into His divine contact.' True to the words, He won the scriptural debate among the pandits of India in Kashi at the age of seven years.</p>

                <p class="font-size-18">He renounced the worldly life at the age of eleven and was known as Nilkanth Varni. During His seven and half years of sojourn in the jungle, He walked the whole span of greater India from greater Himalayas to the end of land, Kanyakumari in South and Assam in East and Gujarat in west. At the end of His entire journey which set the path for the benefit for all and triumph over all evil, He settled in the village of Loj in Saurashtra. He accepted Ramanand Swamiji as a Guru for all humanity:</p>

                <p class="font-size-18">Let me suffer million times but do not let my devotee suffer one bit.My devotee shall never go hungry even if he is the victim of seven famines.I shall take my devotee to the abode of God - Akshardham at the end of His mortal life. I shall be present on this Earth eternally through the God-Ordained Saint who has renounced women, wealth and fame.</p>

                <p class="font-size-18">He initiated 500 Paramhansas (saints) in one night at the age of 21. He was the mentor of millions of persons who had never visualized the happiness that was to be their domain. Most of them were uneducated warriors and farmers. He conquered sword wielding warriors not by wielding sword but immersing them in His divine love.</p>

                <p class="font-size-18">He resurrected them from the brink of barbarism and elevated them to the level of God fearing devotees. He was probably the most advanced reformer who worked at the lowest strata of the society enriching them to be the part of the happy, serene and truthful society. He put an end to practice of Sati, infanticide and many other vices that plagued the society of yore. Joban Pagi, Bhaguji, Alaiya Khachar etc. are the examples of His unflinching effort to revive humanity to it's' best. He wrote 'Shikshapatri', 212 verses of perennial importance that if followed, would bring ethereal peace and prosperity; in the material world and metaphysical world. He also dictated discourses known as 'Vachanamritam', the first compiled prose in the history of Gujarati Language.</p>

                <p class="font-size-18">He left His mortal body at the age of 49 yrs., living an indelible impression on the future. Millions of lives were transformed from human to superlative being through His divine contact.</p>

                <p class="font-size-18">He always said, 'The objective of My manifestation in this world is to cultivate Service and Devotion.' Service unto Humanity and Devotion unto God are the two goals which every follower of Bhagwaan Shree Swaminarayan followed. In a nutshell, He was the propagator of Nishkam-Dharma, the spirituality of desirelessness.</p>
            </div>
        </div>

    </div>
    <div id="leader-details-4" class="leader-details"></div>
    <div class="carousel-item col-xs-12 pt-50">
        <div class="testimonial testimonial-xl testimonial-details-sm text-center testimonials-quote-only">
            <div class="testimonial-quote">
                <blockquote>
                    <p class="text-arapawa">
                        <strong class="font-weight-bold">Bhramswaroop Swami Shree Yogiji Maharaj</strong> <br>
                        <small class="text-arapawa">Jeevan Charitra</small>
                    </p>
                </blockquote>
            </div>


        </div>
    </div>
    </div>

    <script>
        function toggleLeaderDetails(leaderId) {
            var allLeaderDetails = document.querySelectorAll('.leader-details');
            allLeaderDetails.forEach(function(detail) {
                detail.classList.remove('active');
            });

            var leaderDetail = document.getElementById('leader-details-' + leaderId);
            leaderDetail.classList.add('active');
        }
    </script>

</body>

</html>